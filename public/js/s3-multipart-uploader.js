/**
 * S3 Multipart Uploader for Laravel 12 + AWS S3 Direct Upload
 * Optimized for High-Speed Concurrent Uploads (Supports 50MB, 100MB, 500MB, 1GB - 5GB+)
 * Features: Multi-Thread Parallel Upload (4-5 workers), Dynamic Chunk Sizing, Live MB/s, Accurate ETA, Auto-Retry, Cancel
 */
class S3MultipartUploader {
    constructor(options = {}) {
        this.courseId = options.courseId;
        this.lessonId = options.lessonId || null;
        this.createUrl = options.createUrl;
        this.signPartUrl = options.signPartUrl;
        this.batchSignUrl = options.batchSignUrl;
        this.completeUrl = options.completeUrl;
        this.abortUrl = options.abortUrl;
        this.csrfToken = options.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        // Số luồng upload song song (4 luồng giúp tận dụng tối đa băng thông quốc tế)
        this.concurrency = options.concurrency || 4;
        this.maxRetries = options.maxRetries || 3;

        this.onProgress = options.onProgress || (() => {});
        this.onSuccess = options.onSuccess || (() => {});
        this.onError = options.onError || (() => {});
        this.onStatusChange = options.onStatusChange || (() => {});

        this.isCancelled = false;
        this.activeXhrs = [];
        this.uploadId = null;
        this.s3Key = null;
        this.startTime = null;
        this.partProgress = {};
        this.speedSamples = [];
    }

    /**
     * Tự động điều chỉnh kích thước Part tối ưu theo dung lượng file
     */
    getOptimalChunkSize(fileSize) {
        if (fileSize < 50 * 1024 * 1024) {
            return 6 * 1024 * 1024; // 6MB
        } else if (fileSize < 200 * 1024 * 1024) {
            return 8 * 1024 * 1024; // 8MB
        } else if (fileSize < 1024 * 1024 * 1024) {
            return 12 * 1024 * 1024; // 12MB
        } else {
            return 16 * 1024 * 1024; // 16MB
        }
    }

    async upload(file) {
        if (!file) {
            throw new Error('Vui lòng chọn file video.');
        }

        this.isCancelled = false;
        this.activeXhrs = [];
        this.startTime = Date.now();
        this.partProgress = {};
        this.speedSamples = [];
        this.file = file;
        this.chunkSize = this.getOptimalChunkSize(file.size);

        this.onStatusChange('initializing', 'Đang khởi tạo phiên tải lên S3...');

        // 1. Lấy thời lượng video từ metadata
        let duration = await this.getVideoDuration(file).catch(() => 0);

        try {
            // 2. Khởi tạo Multipart Upload trên S3 qua Laravel
            const initResponse = await fetch(this.createUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({
                    filename: file.name,
                    content_type: file.type || 'video/mp4',
                    file_size: file.size,
                    lesson_id: this.lessonId,
                }),
            });

            if (!initResponse.ok) {
                const errData = await initResponse.json().catch(() => ({}));
                throw new Error(errData.message || 'Không thể tạo phiên upload trên S3.');
            }

            const initData = await initResponse.json();
            this.uploadId = initData.uploadId;
            this.s3Key = initData.key;

            if (this.isCancelled) return;

            // 3. Phân chia các Parts tối ưu
            const totalParts = Math.ceil(file.size / this.chunkSize);
            const parts = [];
            for (let i = 0; i < totalParts; i++) {
                const start = i * this.chunkSize;
                const end = Math.min(start + this.chunkSize, file.size);
                parts.push({
                    partNumber: i + 1,
                    start,
                    end,
                    blob: file.slice(start, end),
                    eTag: null,
                });
            }

            this.onStatusChange('uploading', `Đang tải song song ${this.concurrency} luồng (${parts.length} phần)...`);

            // 4. Upload các parts với Concurrency song song
            const completedParts = await this.uploadPartsWithConcurrency(parts);

            if (this.isCancelled) return;

            // 5. Hoàn tất Multipart Upload trên S3
            this.onStatusChange('completing', 'Đang xác thực và ghép file hoàn chỉnh trên S3...');

            const completeResponse = await fetch(this.completeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({
                    key: this.s3Key,
                    uploadId: this.uploadId,
                    parts: completedParts.map(p => ({
                        PartNumber: p.partNumber,
                        ETag: p.eTag,
                    })),
                }),
            });

            if (!completeResponse.ok) {
                const errData = await completeResponse.json().catch(() => ({}));
                throw new Error(errData.message || 'Không thể ghép các phần trên S3.');
            }

            const completeData = await completeResponse.json();

            this.onStatusChange('completed', 'Tải lên S3 hoàn tất thành công!');
            this.onSuccess({
                key: this.s3Key,
                uploadId: this.uploadId,
                filename: file.name,
                size: file.size,
                mime: file.type || 'video/mp4',
                duration: duration,
                location: completeData.location,
            });

        } catch (error) {
            if (this.isCancelled) {
                this.onStatusChange('cancelled', 'Đã hủy tải lên.');
                return;
            }
            this.onStatusChange('error', error.message || 'Lỗi tải lên S3');
            this.onError(error);
            throw error;
        }
    }

    async uploadPartsWithConcurrency(parts) {
        const results = [];
        let index = 0;

        const worker = async () => {
            while (index < parts.length && !this.isCancelled) {
                const currentPart = parts[index++];
                const completedPart = await this.uploadSinglePartWithRetry(currentPart);
                results.push(completedPart);
            }
        };

        const workers = [];
        const threadCount = Math.min(this.concurrency, parts.length);
        for (let i = 0; i < threadCount; i++) {
            workers.push(worker());
        }

        await Promise.all(workers);

        return results.sort((a, b) => a.partNumber - b.partNumber);
    }

    async uploadSinglePartWithRetry(part, attempt = 1) {
        if (this.isCancelled) return null;

        try {
            // Lấy presigned URL cho part
            const signResp = await fetch(this.signPartUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({
                    key: this.s3Key,
                    uploadId: this.uploadId,
                    partNumber: part.partNumber,
                }),
            });

            if (!signResp.ok) {
                throw new Error(`Lỗi ký upload part ${part.partNumber}`);
            }

            const signData = await signResp.json();
            const presignedUrl = signData.url;

            // Upload binary part trực tiếp lên S3
            const eTag = await this.putBlobToS3(presignedUrl, part.blob, part.partNumber);
            part.eTag = eTag;
            return part;

        } catch (err) {
            if (this.isCancelled) return null;

            if (attempt < this.maxRetries) {
                console.warn(`Part ${part.partNumber} thất bại, thử lại lần ${attempt + 1}...`, err);
                await new Promise(res => setTimeout(res, 1000 * attempt));
                return this.uploadSinglePartWithRetry(part, attempt + 1);
            }
            throw new Error(`Tải part ${part.partNumber} thất bại sau ${this.maxRetries} lần thử: ${err.message}`);
        }
    }

    putBlobToS3(url, blob, partNumber) {
        return new Promise((resolve, reject) => {
            if (this.isCancelled) {
                return reject(new Error('Cancelled'));
            }

            const xhr = new XMLHttpRequest();
            this.activeXhrs.push(xhr);

            xhr.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    this.partProgress[partNumber] = e.loaded;
                    this.calculateOverallProgress();
                }
            };

            xhr.onload = () => {
                this.activeXhrs = this.activeXhrs.filter(x => x !== xhr);
                if (xhr.status >= 200 && xhr.status < 300) {
                    let eTag = xhr.getResponseHeader('ETag') || xhr.getResponseHeader('etag');
                    if (eTag) {
                        eTag = eTag.replace(/["']/g, '');
                    }
                    this.partProgress[partNumber] = blob.size;
                    this.calculateOverallProgress();
                    resolve(eTag);
                } else {
                    reject(new Error(`S3 returned HTTP ${xhr.status}`));
                }
            };

            xhr.onerror = () => {
                this.activeXhrs = this.activeXhrs.filter(x => x !== xhr);
                reject(new Error('Mạng gián đoạn khi tải lên S3'));
            };

            xhr.onabort = () => {
                this.activeXhrs = this.activeXhrs.filter(x => x !== xhr);
                reject(new Error('Cancelled'));
            };

            xhr.open('PUT', url, true);
            xhr.send(blob);
        });
    }

    calculateOverallProgress() {
        if (!this.file || this.isCancelled) return;

        let uploadedBytes = 0;
        for (const key in this.partProgress) {
            uploadedBytes += this.partProgress[key] || 0;
        }

        const totalBytes = this.file.size;
        const percent = Math.min(100, Math.round((uploadedBytes / totalBytes) * 100));

        const now = Date.now();
        const elapsedTime = (now - this.startTime) / 1000; // seconds

        // Tính tốc độ trung bình theo cửa sổ trượt (Sliding Window)
        this.speedSamples.push({ time: now, bytes: uploadedBytes });
        if (this.speedSamples.length > 10) {
            this.speedSamples.shift();
        }

        let speed = 0;
        if (this.speedSamples.length >= 2) {
            const first = this.speedSamples[0];
            const last = this.speedSamples[this.speedSamples.length - 1];
            const timeDiff = (last.time - first.time) / 1000;
            const bytesDiff = last.bytes - first.bytes;
            speed = timeDiff > 0 ? (bytesDiff / timeDiff) : (uploadedBytes / elapsedTime);
        } else {
            speed = elapsedTime > 0 ? (uploadedBytes / elapsedTime) : 0;
        }

        const remainingBytes = Math.max(0, totalBytes - uploadedBytes);
        const etaSeconds = speed > 0 ? Math.ceil(remainingBytes / speed) : 0;

        this.onProgress({
            percent,
            uploadedBytes,
            totalBytes,
            uploadedFormatted: this.formatBytes(uploadedBytes),
            totalFormatted: this.formatBytes(totalBytes),
            speedFormatted: this.formatSpeed(speed),
            etaFormatted: this.formatEta(etaSeconds),
            speed,
            etaSeconds,
        });
    }

    cancel() {
        this.isCancelled = true;
        this.activeXhrs.forEach(xhr => {
            try { xhr.abort(); } catch (_) {}
        });
        this.activeXhrs = [];

        if (this.s3Key && this.uploadId) {
            fetch(this.abortUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify({
                    key: this.s3Key,
                    uploadId: this.uploadId,
                }),
            }).catch(() => {});
        }

        this.onStatusChange('cancelled', 'Đã hủy tải lên.');
    }

    getVideoDuration(file) {
        return new Promise((resolve) => {
            const video = document.createElement('video');
            video.preload = 'metadata';
            const url = URL.createObjectURL(file);

            video.onloadedmetadata = () => {
                URL.revokeObjectURL(url);
                resolve(Math.round(video.duration || 0));
            };

            video.onerror = () => {
                URL.revokeObjectURL(url);
                resolve(0);
            };

            video.src = url;
        });
    }

    formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    formatSpeed(bytesPerSecond) {
        return this.formatBytes(bytesPerSecond) + '/s';
    }

    formatEta(seconds) {
        if (!seconds || seconds <= 0) return '0s';
        if (seconds < 60) return seconds + 's';
        const minutes = Math.floor(seconds / 60);
        const remainingSec = seconds % 60;
        return minutes + 'm ' + (remainingSec > 0 ? remainingSec + 's' : '');
    }
}

function createLessonFormState(config) {
    return {
        selectedType: config.selectedType || '',
        isUploading: false,
        uploadProgress: 0,
        uploadedBytesFormatted: '0 B',
        totalBytesFormatted: '0 B',
        uploadSpeedFormatted: '',
        uploadEtaFormatted: '',
        uploadStatus: 'idle',
        uploadStatusMessage: '',
        s3Key: config.s3Key || '',
        videoOriginalName: config.videoOriginalName || '',
        videoSize: config.videoSize || '',
        videoMime: config.videoMime || '',
        uploaderInstance: null,

        startS3Upload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const formElement = event.target.closest('form');

            this.isUploading = true;
            this.uploadProgress = 0;
            this.uploadStatus = 'uploading';
            this.uploadStatusMessage = 'Đang chuẩn bị tải lên S3...';

            this.uploaderInstance = new S3MultipartUploader({
                courseId: config.courseId,
                lessonId: config.lessonId,
                createUrl: config.createUrl,
                signPartUrl: config.signPartUrl,
                completeUrl: config.completeUrl,
                abortUrl: config.abortUrl,
                concurrency: 4, // 4 luồng song song
                onStatusChange: (status, message) => {
                    this.uploadStatus = status;
                    this.uploadStatusMessage = message;
                },
                onProgress: (prog) => {
                    this.uploadProgress = prog.percent;
                    this.uploadedBytesFormatted = prog.uploadedFormatted;
                    this.totalBytesFormatted = prog.totalFormatted;
                    this.uploadSpeedFormatted = prog.speedFormatted;
                    this.uploadEtaFormatted = prog.etaFormatted;
                },
                onSuccess: (data) => {
                    this.isUploading = false;
                    this.uploadStatus = 'completed';
                    this.uploadStatusMessage = '';
                    this.s3Key = data.key;
                    this.videoOriginalName = data.filename;
                    this.videoSize = data.size;
                    this.videoMime = data.mime;

                    if (data.duration && data.duration > 0 && formElement) {
                        const durationInput = formElement.querySelector("input[name='duration']");
                        if (durationInput) {
                            durationInput.value = data.duration;
                            durationInput.classList.add('ring-2', 'ring-emerald-500', 'border-emerald-500');
                            setTimeout(() => durationInput.classList.remove('ring-2', 'ring-emerald-500'), 2500);
                        }
                    }
                },
                onError: (err) => {
                    this.isUploading = false;
                    this.uploadStatus = 'error';
                    this.uploadStatusMessage = err.message || 'Lỗi tải lên S3';
                }
            });

            this.uploaderInstance.upload(file).catch(err => {
                console.error('S3 Upload Error:', err);
            });
        },

        cancelS3Upload() {
            if (this.uploaderInstance) {
                this.uploaderInstance.cancel();
            }
            this.isUploading = false;
            this.uploadProgress = 0;
            this.uploadStatus = 'idle';
            this.uploadStatusMessage = '';
            const fileInput = this.$refs.s3FileInput;
            if (fileInput) fileInput.value = '';
        },

        resetVideoSelection() {
            this.cancelS3Upload();
            this.s3Key = '';
            this.videoOriginalName = '';
            this.videoSize = '';
            this.videoMime = '';
        }
    };
}

if (typeof window !== 'undefined') {
    window.S3MultipartUploader = S3MultipartUploader;
    window.createLessonFormState = createLessonFormState;
}
