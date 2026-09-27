# Nhật ký Phát triển & Tài liệu Kỹ thuật DATN OnlineFEA

## 1. Khởi tạo Yêu cầu Hệ thống (2026-06-22)
- Xác định mục tiêu đồ án tốt nghiệp hệ thống học trực tuyến OnlineFEA.
- Đặc tả các module chính: Xác thực, Quản lý Khóa học, Bài học, Video Player, Quizzes, Giảng viên và Quản trị viên.

## 2. Thiết kế Kiến trúc Hệ thống (2026-06-23)
- Mô hình kiến trúc Laravel MVC kết hợp Blade Components và TailwindCSS.
- Cấu trúc quan hệ cơ sở dữ liệu giữa Courses, Chapters, Lessons, Quizzes và Users.

## 3. Phân quyền và Vai trò Người dùng (2026-06-24)
- Thiết lập ma trận phân quyền: Admin, Instructor, Student, Guest.
- Định nghĩa quyền hạn chi tiết cho từng tác vụ quản lý khóa học và phê duyệt nội dung.

## 4. Quy trình Nghiệp vụ Quản lý Khóa học (2026-06-25)
- Luồng tạo khóa học, tải bài giảng video HLS/MP4 và đính kèm tài liệu học tập.
- Luồng đánh giá và phê duyệt khóa học trước khi xuất bản công khai.

## 5. Đặc tả Giao diện & Chuẩn Giao tiếp (2026-06-26)
- Định dạng phản hồi JSON cho các API bất đồng bộ (AJAX wishlist, progress tracking, quiz submit).
- Quy chuẩn mã lỗi và thông báo người dùng theo chuẩn RESTful.

## 6. Chuẩn hóa Cấu trúc Mã nguồn (2026-07-13)
- Tối ưu cấu trúc các Controller, Form Requests và View Components.
- Định dạng mã nguồn theo tiêu chuẩn PSR-12 và Laravel Code Style.

## 7. Hướng dẫn Môi trường Phát triển (2026-07-15)
- Hướng dẫn cài đặt PHP 8.2+, Composer, Node.js và MySQL.
- Cấu hình file .env và các bước chạy Migration, Seeder mẫu.

## 8. Kế hoạch Kiểm thử Đơn vị (2026-07-17)
- Viết tài liệu kiểm thử cho Middleware phân quyền vai trò (RoleMiddleware).
- Xác thực luồng chuyển hướng khi truy cập trái phép trang quản trị.

## 9. Tối ưu Giao diện Responsive Học viên (2026-07-27)
- Thiết kế tối ưu hiển thị trên các kích thước màn hình Mobile, Tablet (768px, 1024px) và Desktop.
- Đảm bảo trải nghiệm xem video bài giảng và làm bài quiz mượt mà.

## 10. Tái cấu trúc Blade Components (2026-07-28)
- Tách các thành phần Header, Sidebar, Stat Card, Progress Bar thành component dùng chung.
- Giảm thiểu lặp mã và tăng tính nhất quán giao diện.

## 11. Tài liệu Trải nghiệm Người dùng UI/UX (2026-07-29)
- Quy chuẩn bảng màu, khoảng cách (spacing), typography theo hệ thống Coursera/Udemy standard.
- Hướng dẫn các trạng thái rỗng (empty state) và chỉ báo tải (loading spinner).

## 12. Đặc tả Kiểm thử Yêu thích Khóa học (2026-07-30)
- Test case thêm/xóa khóa học khỏi Wishlist bất đồng bộ không tải lại trang.
- Test case kiểm tra trạng thái nút yêu thích hiển thị đồng bộ trên Header và Danh mục.

## 13. Tinh chỉnh Hiệu ứng & Animation (2026-07-31)
- Thêm hiệu ứng transition mượt mà khi đóng mở Sidebar trên Mobile.
- Tối ưu hiệu ứng hover trên thẻ khóa học (Course Card) và nút thao tác.

## 14. Dọn dẹp & Tối ưu CSS Bundle (2026-07-31)
- Loại bỏ các class CSS trùng lặp, tối ưu bundle size khi build qua Vite.
- Đảm bảo tốc độ hiển thị First Contentful Paint (FCP) dưới 1.2s.

## 15. Cấu hình Dịch vụ Lưu trữ & CDN (2026-08-03)
- Hướng dẫn cấu hình S3 / Google Storage cho video HLS và tài liệu đính kèm.
- Cơ chế bảo mật đường dẫn video có chữ ký điện tử (Signed URL).

## 16. Kiểm thử Tích hợp Luồng Học tập (2026-08-04)
- Kiểm thử luồng học từ Bài 1 đến Bài kết thúc, ghi nhận tiến độ LessonProgress.
- Kiểm thử chấm điểm tự động cho Quiz và điều kiện nhận chứng chỉ hoàn thành khóa học.

## 17. Tối ưu Truy vấn & Performance (2026-08-06)
- Áp dụng Eager Loading (`with('chapters.lessons')`) giải quyết triệt để lỗi N+1 Query.
- Thêm Index cho các trường lọc thường dùng: `category_id`, `status`, `level`, `price`.

## 18. Tổng kết Tiến độ Sprint 3 & Sẵn sàng Hoàn thiện (2026-08-07)
- Hoàn thành 100% các tính năng học viên, giảng viên và quản trị viên.
- Chuẩn bị dữ liệu mẫu quy mô lớn phục vụ nghiệm thu đồ án tốt nghiệp.
