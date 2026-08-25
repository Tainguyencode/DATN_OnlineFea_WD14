/**
 * S3 Multipart Uploader for Laravel 12 + AWS S3 Direct Upload
 * Optimized for High-Speed Concurrent Uploads (Supports 50MB, 100MB, 500MB, 1GB - 5GB+)
 * Features: Multi-Thread Parallel Upload (4-5 workers), Dynamic Chunk Sizing, Live MB/s, Accurate ETA, Auto-Retry, Cancel
 */
const S3_UPLOAD_PUBLIC_ERROR = 'Không thể tải video lên lúc này. Vui lòng thử lại.';

function createS3UploadUserError(message) {
    const error = new Error(message);
    error.isUserFacing = true;

    return error;
}

function toS3UploadUserError(error) {
    if (error?.isUserFacing && typeof error.message === 'string' && error.message.trim() !== '') {
        return error;
    }

    return createS3UploadUserError(S3_UPLOAD_PUBLIC_ERROR);
}

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

        this.onInit = options.onInit || (() => {});
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
            throw createS3UploadUserError('Vui lòng chọn tệp video để tải lên.');
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
                throw createS3UploadUserError(
                    typeof errData.message === 'string' && errData.message.trim() !== ''
                        ? errData.message
                        : S3_UPLOAD_PUBLIC_ERROR
                );
            }

            const initData = await initResponse.json();
            this.uploadId = initData.uploadId;
            this.s3Key = initData.key;

            if (typeof this.onInit === 'function') {
                this.onInit(initData);
            }

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
                throw createS3UploadUserError(
                    typeof errData.message === 'string' && errData.message.trim() !== ''
                        ? errData.message
                        : S3_UPLOAD_PUBLIC_ERROR
                );
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
            const userFacingError = toS3UploadUserError(error);
            this.onStatusChange('error', userFacingError.message);
            this.onError(userFacingError);
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

    async abortMultipartUpload() {
        if (!this.uploadId || !this.s3Key) return;
        try {
            await fetch(this.abortUrl, {
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
            });
        } catch (e) {
            console.warn('S3 AbortMultipartUpload failed:', e);
        }
    }

    cancel() {
        this.isCancelled = true;
        this.activeXhrs.forEach(xhr => {
            try { xhr.abort(); } catch (e) {}
        });
        this.activeXhrs = [];
        this.abortMultipartUpload();
        this.onStatusChange('cancelled', 'Đã hủy tải lên.');
    }

    getVideoDuration(file) {
        return new Promise((resolve) => {
            const video = document.createElement('video');
            video.preload = 'metadata';
            const url = URL.createObjectURL(file);

            video.onloadedmetadata = function () {
                URL.revokeObjectURL(url);
                const dur = Math.round(video.duration || 0);
                resolve(dur);
            };

            video.onerror = function () {
                URL.revokeObjectURL(url);
                resolve(0);
            };

            video.src = url;
        });
    }

    formatBytes(bytes, decimals = 2) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

    formatSpeed(bytesPerSecond) {
        if (!bytesPerSecond || bytesPerSecond <= 0) return '0 B/s';
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

class CourseUploadQueueManager {
    constructor() {
        this.queue = [];
        this.activeUploader = null;
    }

    addUpload(item) {
        const queueItem = {
            id: item.id || ('upl_' + Date.now() + '_' + Math.random().toString(36).substring(2, 7)),
            title: item.title || item.file.name,
            filename: item.file.name,
            file: item.file,
            size: item.file.size,
            percent: 0,
            status: 'queued', // 'queued' | 'uploading' | 'completed' | 'error'
            statusText: 'Chờ tải',
            key: item.key || '',
            uploadId: item.uploadId || '',
            config: item,
            uploader: null
        };

        this.queue.push(queueItem);
        this.render();
        this.processNext();
        return queueItem;
    }

    processNext() {
        const active = this.queue.find(i => i.status === 'uploading');
        if (active) return;

        const next = this.queue.find(i => i.status === 'queued');
        if (!next) {
            this.render();
            return;
        }

        next.status = 'uploading';
        next.statusText = 'Đang tải';
        this.render();

        const uploader = new S3MultipartUploader({
            courseId: next.config.courseId,
            lessonId: next.config.lessonId,
            createUrl: next.config.createUrl,
            signPartUrl: next.config.signPartUrl,
            completeUrl: next.config.completeUrl,
            abortUrl: next.config.abortUrl,
            concurrency: 4,
            onInit: (initData) => {
                next.key = initData.key;
                next.uploadId = initData.uploadId;
                if (typeof next.config.onInit === 'function') {
                    next.config.onInit(initData);
                }
            },
            onProgress: (prog) => {
                next.percent = prog.percent;
                next.statusText = `${prog.percent}%`;
                this.render();

                if (typeof next.config.onProgress === 'function') {
                    next.config.onProgress(prog);
                }
            },
            onSuccess: (data) => {
                next.status = 'completed';
                next.key = data.key;

                if (typeof next.config.onSuccess === 'function') {
                    next.config.onSuccess(data);
                }

                this.queue = this.queue.filter(i => i.id !== next.id);
                this.render();

                if (window.triggerHlsPolling) {
                    window.triggerHlsPolling();
                }

                this.processNext();
            },
            onError: (err) => {
                next.status = 'error';
                next.statusText = 'Lỗi tải lên';
                this.render();

                if (typeof next.config.onError === 'function') {
                    next.config.onError(err);
                }

                this.processNext();
            }
        });

        uploader.s3Key = next.key || null;
        next.uploader = uploader;
        this.activeUploader = uploader;

        uploader.upload(next.file).catch(err => {
            console.error('Queue upload error for', next.filename, err);
        });
    }

    cancelUpload(itemId) {
        const item = this.queue.find(i => i.id === itemId);
        if (!item) return;

        if (item.uploader) {
            item.uploader.cancel();
        }

        this.queue = this.queue.filter(i => i.id !== itemId);
        this.render();
        this.processNext();
    }

    render() {
        const panel = document.getElementById('global-video-upload-queue-panel');
        if (!panel) return;

        const activeItems = this.queue.filter(i => i.status === 'uploading' || i.status === 'queued');

        if (activeItems.length === 0) {
            panel.classList.add('hidden');
            panel.innerHTML = '';
            return;
        }

        panel.classList.remove('hidden');

        let html = `
            <div class="rounded-xl border border-indigo-200 bg-indigo-50/80 p-4 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-xs font-black uppercase tracking-wider text-indigo-950 flex items-center gap-2">
                        <svg class="animate-spin h-3.5 w-3.5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>VIDEO ĐANG TẢI</span>
                    </h4>
                    <span class="text-xs font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-full">${activeItems.length} video</span>
                </div>
                <div class="space-y-2">
        `;

        activeItems.forEach(item => {
            const isUploading = item.status === 'uploading';
            html += `
                <div class="flex items-center justify-between bg-white rounded-lg p-3 border border-indigo-100 shadow-2xs text-xs">
                    <div class="min-w-0 flex-1 pr-3">
                        <div class="flex items-center justify-between font-bold text-slate-800 mb-1">
                            <span class="truncate max-w-xs">${item.title}</span>
                            <span class="font-mono text-indigo-600">${isUploading ? (item.percent + '%') : 'Chờ tải'}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-500 to-emerald-500 h-2 rounded-full transition-all duration-300" style="width: ${item.percent}%"></div>
                        </div>
                    </div>
                    <div class="shrink-0 flex items-center gap-2">
                        <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold ${isUploading ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600'}">
                            ${isUploading ? 'Đang tải' : 'Đang chờ'}
                        </span>
                        <button type="button" onclick="window.CourseUploadQueue.cancelUpload('${item.id}')" class="text-rose-600 hover:text-rose-700 font-bold hover:underline cursor-pointer text-[11px]">Hủy</button>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        panel.innerHTML = html;
    }
}

const CourseUploadQueue = new CourseUploadQueueManager();

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
        processingStatus: config.processingStatus || '',
        hlsManifestKey: config.hlsManifestKey || '',
        videoPath: config.videoPath || '',
        courseId: config.courseId,
        lessonId: config.lessonId || null,
        createUrl: config.createUrl,
        signPartUrl: config.signPartUrl,
        completeUrl: config.completeUrl,
        abortUrl: config.abortUrl,
        currentQueueId: null,

        generateS3Key(filename) {
            const ext = (filename.split('.').pop() || 'mp4').toLowerCase();
            const safeExt = ['mp4', 'mov', 'avi', 'webm', 'mkv', 'm4v'].includes(ext) ? ext : 'mp4';
            const uuid = 'u' + Date.now().toString(36) + Math.random().toString(36).substring(2, 7);
            const lessonPart = this.lessonId ? this.lessonId : ('temp_' + Date.now().toString(36));
            return `originals/courses/${this.courseId}/lessons/${lessonPart}/${uuid}.${safeExt}`;
        },

        startS3Upload(event) {
            const file = event.target.files && event.target.files[0];
            if (!file) return;

            const formElement = event.target.closest('form');
            const titleInput = formElement ? formElement.querySelector("input[name='title']") : null;
            const lessonTitle = (titleInput && titleInput.value.trim()) ? titleInput.value.trim() : file.name;

            const preKey = this.generateS3Key(file.name);
            this.s3Key = preKey;
            this.videoOriginalName = file.name;
            this.videoSize = file.size;
            this.videoMime = file.type || 'video/mp4';

            this.isUploading = true;
            this.uploadProgress = 0;
            this.uploadStatus = 'uploading';
            this.uploadStatusMessage = 'Đã đưa vào hàng chờ tải lên...';

            const queueItem = CourseUploadQueue.addUpload({
                title: lessonTitle,
                file: file,
                key: preKey,
                courseId: this.courseId,
                lessonId: this.lessonId,
                createUrl: this.createUrl,
                signPartUrl: this.signPartUrl,
                completeUrl: this.completeUrl,
                abortUrl: this.abortUrl,
                onInit: (initData) => {
                    this.s3Key = initData.key;
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
                    this.uploadStatusMessage = toS3UploadUserError(err).message;
                }
            });

            this.currentQueueId = queueItem.id;
        },

        async submitLessonForm(event) {
            const form = event.target;
            const isCreateForm = !this.lessonId;

            // Submit qua AJAX cho cả Create và Edit để trang không reload làm đứt kết nối upload video
            if (window.fetch) {
                event.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const origBtnText = submitBtn ? submitBtn.innerHTML : '';
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="flex items-center gap-1.5"><svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span>Đang lưu...</span></span>';
                }

                try {
                    const formData = new FormData(form);
                    if (this.s3Key) {
                        formData.set('s3_key', this.s3Key);
                    }
                    if (this.videoOriginalName) {
                        formData.set('video_original_name', this.videoOriginalName);
                    }
                    if (this.videoSize) {
                        formData.set('video_size', this.videoSize);
                    }
                    if (this.videoMime) {
                        formData.set('video_mime', this.videoMime);
                    }

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    if (response.ok) {
                        const resData = await response.json();

                        if (isCreateForm) {
                            // Reset các ô nhập liệu của form tạo mới
                            const titleInput = form.querySelector('input[name="title"]');
                            if (titleInput) titleInput.value = '';

                            const durationInput = form.querySelector('input[name="duration"]');
                            if (durationInput) durationInput.value = '';

                            const contentInput = form.querySelector('textarea[name="content"]');
                            if (contentInput) contentInput.value = '';

                            const fileInput = this.$refs.s3FileInput;
                            if (fileInput) fileInput.value = '';

                            this.s3Key = '';
                            this.videoOriginalName = '';
                            this.videoSize = '';
                            this.videoMime = '';
                            this.isUploading = false;
                            this.uploadProgress = 0;
                            this.uploadStatus = 'idle';
                            this.uploadStatusMessage = '';
                        }

                        if (window.triggerHlsPolling) {
                            window.triggerHlsPolling();
                        }

                        // Hiển thị thông báo thành công
                        if (window.showCurriculumToast) {
                            const msg = isCreateForm
                                ? 'Đã lưu bài học thành công! Video đang tiếp tục tải lên trong hàng chờ.'
                                : 'Đã cập nhật bài học thành công! Video đang tiếp tục tải lên trong hàng chờ.';
                            window.showCurriculumToast(msg);
                        }
                    } else {
                        form.submit();
                    }
                } catch (e) {
                    console.error('AJAX lesson submit error, falling back:', e);
                    form.submit();
                } finally {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = origBtnText;
                    }
                }
            }
        },

        cancelS3Upload() {
            if (this.currentQueueId) {
                CourseUploadQueue.cancelUpload(this.currentQueueId);
                this.currentQueueId = null;
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
            this.uploadStatus = 'idle';
            this.uploadStatusMessage = '';
        }
    };
}

function showCurriculumToast(message, isError = false) {
    let toast = document.getElementById('curriculum-floating-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'curriculum-floating-toast';
        toast.className = 'fixed bottom-5 right-5 z-50 transition-all duration-300 transform translate-y-10 opacity-0 pointer-events-none';
        document.body.appendChild(toast);
    }

    toast.innerHTML = `
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg border text-sm font-bold ${isError ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-emerald-600 text-white border-emerald-700'}">
            <span>${isError ? '⚠️' : '✅'}</span>
            <span>${message}</span>
        </div>
    `;

    toast.classList.remove('translate-y-10', 'opacity-0', 'pointer-events-none');
    toast.classList.add('translate-y-0', 'opacity-100');

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-10', 'opacity-0', 'pointer-events-none');
    }, 4000);
}

/**
 * Polling trạng thái HLS tự động trên trang Curriculum cấp Course
 * Cập nhật DUY NHẤT 1 Banner thông báo chung và khóa/mở nút "Gửi duyệt"
 */
function initCurriculumHlsPolling(hlsStatusUrl) {
    if (!hlsStatusUrl) return;

    let pollInterval = null;

    async function checkStatus() {
        try {
            const response = await fetch(hlsStatusUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) return;

            const data = await response.json();
            const commonState = data.common_state || 'completed'; // 'completed' | 'processing' | 'failed'
            const commonMessage = data.common_message || '';
            const canSubmit = !!data.can_submit;

            // 1. CẬP NHẬT BANNER HLS CHUNG TỔNG THỂ
            const bannerWrapper = document.getElementById('common-hls-banner-wrapper');
            const messageEl = document.getElementById('common-hls-message');
            const iconEl = document.getElementById('common-hls-icon');

            if (bannerWrapper && messageEl) {
                messageEl.textContent = commonMessage;

                if (commonState === 'completed') {
                    bannerWrapper.className = 'rounded-xl border border-emerald-200 bg-emerald-50/80 p-4 text-emerald-900 shadow-xs transition-all duration-300';
                    if (iconEl) iconEl.textContent = '✅';
                } else if (commonState === 'failed') {
                    bannerWrapper.className = 'rounded-xl border border-rose-200 bg-rose-50/80 p-4 text-rose-900 shadow-xs transition-all duration-300';
                    if (iconEl) iconEl.textContent = '⚠️';
                } else {
                    // processing
                    bannerWrapper.className = 'rounded-xl border border-amber-200 bg-amber-50/80 p-4 text-amber-900 shadow-xs transition-all duration-300';
                    if (iconEl) iconEl.textContent = '⏳';
                }
            }

            // 2. CẬP NHẬT TRẠNG THÁI HLS CHI TIẾT BÊN CẠNH TỪNG VIDEO BÀI HỌC
            const statuses = data.statuses || {};
            for (const key in statuses) {
                const info = statuses[key];
                const elements = document.querySelectorAll(`[data-hls-status-key="${key}"]`);
                elements.forEach(el => {
                    if (info.is_ready) {
                        el.className = 'font-semibold text-emerald-600';
                        el.textContent = 'Video đã được xử lý bảo mật thành công.';
                        el.removeAttribute('data-hls-processing');
                    } else if (info.is_failed) {
                        el.className = 'font-semibold text-rose-600';
                        el.textContent = 'Video xử lý bảo mật thất bại.';
                        el.removeAttribute('data-hls-processing');
                    } else if (info.is_processing) {
                        el.className = 'font-semibold text-amber-600';
                        el.textContent = 'Video đang trong quá trình xử lý bảo mật. Vui lòng chờ trong giây lát.';
                        el.setAttribute('data-hls-processing', 'true');
                    }
                });
            }

            // 2. KHÓA / MỞ NÚT "GỬI DUYỆT" TRÊN TOÀN BỘ TRANG (Requirement 15, 16)
            const submitButtons = document.querySelectorAll('#curriculum-submit-review-btn, #readinessSubmitForm button[type="submit"], [data-submit-review-btn]');
            submitButtons.forEach(btn => {
                if (canSubmit) {
                    btn.removeAttribute('disabled');
                    btn.classList.remove('bg-slate-300', 'text-slate-500', 'cursor-not-allowed');
                    btn.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'text-white', 'cursor-pointer');
                    btn.removeAttribute('title');
                } else {
                    btn.setAttribute('disabled', 'disabled');
                    btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'text-white', 'cursor-pointer');
                    btn.classList.add('bg-slate-300', 'text-slate-500', 'cursor-not-allowed');
                    btn.setAttribute('title', 'Khóa học chưa thể gửi duyệt vì video vẫn đang được xử lý bảo mật.');
                }
            });

            // Nếu toàn bộ HLS đã hoàn tất hoặc thất bại và không có upload nào đang chạy thì ngắt polling
            const hasActiveUploads = CourseUploadQueue.queue.some(i => i.status === 'uploading' || i.status === 'queued');
            if (commonState === 'completed' && !hasActiveUploads && pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        } catch (e) {
            console.warn('HLS status poll error:', e);
        }
    }

    window.triggerHlsPolling = () => {
        checkStatus();
        if (!pollInterval) {
            pollInterval = setInterval(checkStatus, 5000);
        }
    };

    // Bắt đầu polling ngay khi tải trang
    checkStatus();
    pollInterval = setInterval(checkStatus, 5000);
}

if (typeof window !== 'undefined') {
    window.S3MultipartUploader = S3MultipartUploader;
    window.CourseUploadQueue = CourseUploadQueue;
    window.createLessonFormState = createLessonFormState;
    window.initCurriculumHlsPolling = initCurriculumHlsPolling;
    window.showCurriculumToast = showCurriculumToast;

    // Cảnh báo người dùng nếu reload hoặc thoát trang trong lúc video đang upload
    window.addEventListener('beforeunload', function (e) {
        if (window.CourseUploadQueue && window.CourseUploadQueue.queue) {
            const hasActive = window.CourseUploadQueue.queue.some(i => i.status === 'uploading' || i.status === 'queued');
            if (hasActive) {
                e.preventDefault();
                e.returnValue = 'Video bài học vẫn đang được tải lên nền. Nếu bạn rời khỏi hoặc tải lại trang, tiến trình tải video sẽ bị gián đoạn.';
                return e.returnValue;
            }
        }
    });
}
