# Xử lý HLS nhanh

`HLS_FAST_COPY=true` (mặc định) cho phép giữ nguyên luồng hình khi FFprobe xác nhận H.264, yuv420p, progressive, profile Baseline/Main/High, level không quá 4.2 và không có metadata xoay hình. Âm thanh AAC-LC tối đa hai kênh được giữ nguyên; âm thanh khác chuyển sang AAC. Video không có tiếng vẫn được hỗ trợ.

FFmpeg đóng gói HLS theo keyframe hiện có. Sau đó hệ thống kiểm tra ENDLIST, thời lượng, sự tồn tại và dung lượng của từng segment. Nếu segment dài hơn hai lần thời lượng cấu hình (cộng dung sai 0,5 giây), hoặc nhánh nhanh lỗi, các file của lần thử được bỏ và hệ thống mã hóa lại bằng libx264 như trước. Nhánh copy không khai báo INDEPENDENT-SEGMENTS vì không ép lại keyframe/GOP. Video có metadata không rõ luôn dùng CPU.

Log `[HlsVideoService] HLS conversion finished` có `mode`:

- `stream_copy`: giữ nguyên hình và tiếng.
- `video_copy_audio_encode`: giữ hình, chuyển tiếng.
- `cpu_encode`: mã hóa tương thích bằng CPU, gồm cả fallback.

Nhánh này không sử dụng GPU, không thay đổi phân quyền, S3 hoặc callback xử lý bài học. Thời gian tải video gốc từ S3 và upload kết quả vẫn phụ thuộc mạng. Không cam kết thời gian cho mọi dung lượng/codec. Giới hạn thời gian thử copy là 300 giây; nhánh CPU giữ timeout cấu hình cũ.

Nếu muốn quay về cơ chế cũ, đặt `HLS_FAST_COPY=false`. Sau khi thay cấu hình, xóa config cache nếu có và khởi động lại worker đang chạy. Video đã hoàn thành không tự chuyển đổi lại.

Kiểm thử media thật: `tests/Feature/HlsFastPackagingTest.php`; không dùng database hay S3, nhưng khi chạy PHPUnit vẫn phải đặt tên database kiểm thử `web_onlinefea_test` theo guard của ứng dụng. Bao gồm copy AAC, không âm thanh, chuyển MP3, video không tương thích, GOP thưa, tắt fast copy và đo hai nhánh trên cùng nguồn. Đầu ra các ca chức năng được giải mã bằng FFmpeg với `-xerror`; chưa thay thế kiểm thử tua/phát trên các trình duyệt thực tế.
