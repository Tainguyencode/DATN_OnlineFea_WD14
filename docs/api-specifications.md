# ĐẶC TẢ GIAO DIỆN LẬP TRÌNH ỨNG DỤNG (RESTFUL API SPECIFICATIONS)

## 1. DANH SÁCH ENDPOINTS VÀ ĐẶC TẢ DỮ LIỆU ĐẦU VÀO/ĐẦU RA

### 2.1. Endpoint API Quản lý Module #1: `/api/v1/resource-module-1`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 1, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 1, "name": "Module 1", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_1`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule1.

### 2.2. Endpoint API Quản lý Module #2: `/api/v1/resource-module-2`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 2, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 2, "name": "Module 2", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_2`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule2.

### 2.3. Endpoint API Quản lý Module #3: `/api/v1/resource-module-3`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 3, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 3, "name": "Module 3", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_3`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule3.

### 2.4. Endpoint API Quản lý Module #4: `/api/v1/resource-module-4`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 4, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 4, "name": "Module 4", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_4`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule4.

### 2.5. Endpoint API Quản lý Module #5: `/api/v1/resource-module-5`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 5, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 5, "name": "Module 5", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_5`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule5.

### 2.6. Endpoint API Quản lý Module #6: `/api/v1/resource-module-6`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 6, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 6, "name": "Module 6", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_6`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule6.

### 2.7. Endpoint API Quản lý Module #7: `/api/v1/resource-module-7`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 7, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 7, "name": "Module 7", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_7`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule7.

### 2.8. Endpoint API Quản lý Module #8: `/api/v1/resource-module-8`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 8, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 8, "name": "Module 8", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_8`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule8.

### 2.9. Endpoint API Quản lý Module #9: `/api/v1/resource-module-9`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 9, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 9, "name": "Module 9", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_9`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule9.

### 2.10. Endpoint API Quản lý Module #10: `/api/v1/resource-module-10`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 10, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 10, "name": "Module 10", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_10`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule10.

### 2.11. Endpoint API Quản lý Module #11: `/api/v1/resource-module-11`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 11, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 11, "name": "Module 11", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_11`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule11.

### 2.12. Endpoint API Quản lý Module #12: `/api/v1/resource-module-12`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 12, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 12, "name": "Module 12", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_12`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule12.

### 2.13. Endpoint API Quản lý Module #13: `/api/v1/resource-module-13`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 13, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 13, "name": "Module 13", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_13`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule13.

### 2.14. Endpoint API Quản lý Module #14: `/api/v1/resource-module-14`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 14, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 14, "name": "Module 14", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_14`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule14.

### 2.15. Endpoint API Quản lý Module #15: `/api/v1/resource-module-15`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 15, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 15, "name": "Module 15", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_15`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule15.

### 2.16. Endpoint API Quản lý Module #16: `/api/v1/resource-module-16`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 16, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 16, "name": "Module 16", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_16`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule16.

### 2.17. Endpoint API Quản lý Module #17: `/api/v1/resource-module-17`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 17, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 17, "name": "Module 17", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_17`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule17.

### 2.18. Endpoint API Quản lý Module #18: `/api/v1/resource-module-18`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 18, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 18, "name": "Module 18", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_18`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule18.

### 2.19. Endpoint API Quản lý Module #19: `/api/v1/resource-module-19`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 19, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 19, "name": "Module 19", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_19`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule19.

### 2.20. Endpoint API Quản lý Module #20: `/api/v1/resource-module-20`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 20, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 20, "name": "Module 20", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_20`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule20.

### 2.21. Endpoint API Quản lý Module #21: `/api/v1/resource-module-21`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 21, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 21, "name": "Module 21", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_21`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule21.

### 2.22. Endpoint API Quản lý Module #22: `/api/v1/resource-module-22`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 22, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 22, "name": "Module 22", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_22`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule22.

### 2.23. Endpoint API Quản lý Module #23: `/api/v1/resource-module-23`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 23, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 23, "name": "Module 23", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_23`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule23.

### 2.24. Endpoint API Quản lý Module #24: `/api/v1/resource-module-24`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 24, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 24, "name": "Module 24", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_24`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule24.

### 2.25. Endpoint API Quản lý Module #25: `/api/v1/resource-module-25`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 25, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 25, "name": "Module 25", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_25`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule25.

### 2.26. Endpoint API Quản lý Module #26: `/api/v1/resource-module-26`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 26, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 26, "name": "Module 26", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_26`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule26.

### 2.27. Endpoint API Quản lý Module #27: `/api/v1/resource-module-27`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 27, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 27, "name": "Module 27", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_27`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule27.

### 2.28. Endpoint API Quản lý Module #28: `/api/v1/resource-module-28`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 28, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 28, "name": "Module 28", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_28`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule28.

### 2.29. Endpoint API Quản lý Module #29: `/api/v1/resource-module-29`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 29, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 29, "name": "Module 29", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_29`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule29.

### 2.30. Endpoint API Quản lý Module #30: `/api/v1/resource-module-30`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 30, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 30, "name": "Module 30", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_30`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule30.

### 2.31. Endpoint API Quản lý Module #31: `/api/v1/resource-module-31`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 31, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 31, "name": "Module 31", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_31`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule31.

### 2.32. Endpoint API Quản lý Module #32: `/api/v1/resource-module-32`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 32, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 32, "name": "Module 32", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_32`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule32.

### 2.33. Endpoint API Quản lý Module #33: `/api/v1/resource-module-33`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 33, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 33, "name": "Module 33", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_33`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule33.

### 2.34. Endpoint API Quản lý Module #34: `/api/v1/resource-module-34`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 34, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 34, "name": "Module 34", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_34`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule34.

### 2.35. Endpoint API Quản lý Module #35: `/api/v1/resource-module-35`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 35, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 35, "name": "Module 35", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_35`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule35.

### 2.36. Endpoint API Quản lý Module #36: `/api/v1/resource-module-36`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 36, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 36, "name": "Module 36", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_36`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule36.

### 2.37. Endpoint API Quản lý Module #37: `/api/v1/resource-module-37`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 37, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 37, "name": "Module 37", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_37`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule37.

### 2.38. Endpoint API Quản lý Module #38: `/api/v1/resource-module-38`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 38, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 38, "name": "Module 38", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_38`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule38.

### 2.39. Endpoint API Quản lý Module #39: `/api/v1/resource-module-39`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 39, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 39, "name": "Module 39", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_39`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule39.

### 2.40. Endpoint API Quản lý Module #40: `/api/v1/resource-module-40`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 40, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 40, "name": "Module 40", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_40`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule40.

### 2.41. Endpoint API Quản lý Module #41: `/api/v1/resource-module-41`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 41, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 41, "name": "Module 41", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_41`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule41.

### 2.42. Endpoint API Quản lý Module #42: `/api/v1/resource-module-42`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 42, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 42, "name": "Module 42", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_42`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule42.

### 2.43. Endpoint API Quản lý Module #43: `/api/v1/resource-module-43`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 43, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 43, "name": "Module 43", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_43`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule43.

### 2.44. Endpoint API Quản lý Module #44: `/api/v1/resource-module-44`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 44, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 44, "name": "Module 44", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_44`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule44.

### 2.45. Endpoint API Quản lý Module #45: `/api/v1/resource-module-45`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 45, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 45, "name": "Module 45", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_45`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule45.

### 2.46. Endpoint API Quản lý Module #46: `/api/v1/resource-module-46`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 46, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 46, "name": "Module 46", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_46`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule46.

### 2.47. Endpoint API Quản lý Module #47: `/api/v1/resource-module-47`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 47, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 47, "name": "Module 47", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_47`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule47.

### 2.48. Endpoint API Quản lý Module #48: `/api/v1/resource-module-48`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 48, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 48, "name": "Module 48", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_48`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule48.

### 2.49. Endpoint API Quản lý Module #49: `/api/v1/resource-module-49`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 49, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 49, "name": "Module 49", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_49`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule49.

### 2.50. Endpoint API Quản lý Module #50: `/api/v1/resource-module-50`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 50, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 50, "name": "Module 50", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_50`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule50.

### 2.51. Endpoint API Quản lý Module #51: `/api/v1/resource-module-51`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 51, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 51, "name": "Module 51", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_51`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule51.

### 2.52. Endpoint API Quản lý Module #52: `/api/v1/resource-module-52`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 52, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 52, "name": "Module 52", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_52`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule52.

### 2.53. Endpoint API Quản lý Module #53: `/api/v1/resource-module-53`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 53, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 53, "name": "Module 53", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_53`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule53.

### 2.54. Endpoint API Quản lý Module #54: `/api/v1/resource-module-54`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 54, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 54, "name": "Module 54", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_54`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule54.

### 2.55. Endpoint API Quản lý Module #55: `/api/v1/resource-module-55`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 55, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 55, "name": "Module 55", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_55`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule55.

### 2.56. Endpoint API Quản lý Module #56: `/api/v1/resource-module-56`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 56, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 56, "name": "Module 56", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_56`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule56.

### 2.57. Endpoint API Quản lý Module #57: `/api/v1/resource-module-57`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 57, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 57, "name": "Module 57", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_57`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule57.

### 2.58. Endpoint API Quản lý Module #58: `/api/v1/resource-module-58`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 58, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 58, "name": "Module 58", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_58`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule58.

### 2.59. Endpoint API Quản lý Module #59: `/api/v1/resource-module-59`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 59, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 59, "name": "Module 59", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_59`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule59.

### 2.60. Endpoint API Quản lý Module #60: `/api/v1/resource-module-60`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 60, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 60, "name": "Module 60", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_60`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule60.

### 2.61. Endpoint API Quản lý Module #61: `/api/v1/resource-module-61`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 61, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 61, "name": "Module 61", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_61`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule61.

### 2.62. Endpoint API Quản lý Module #62: `/api/v1/resource-module-62`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 62, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 62, "name": "Module 62", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_62`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule62.

### 2.63. Endpoint API Quản lý Module #63: `/api/v1/resource-module-63`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 63, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 63, "name": "Module 63", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_63`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule63.

### 2.64. Endpoint API Quản lý Module #64: `/api/v1/resource-module-64`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 64, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 64, "name": "Module 64", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_64`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule64.

### 2.65. Endpoint API Quản lý Module #65: `/api/v1/resource-module-65`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 65, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 65, "name": "Module 65", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_65`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule65.

### 2.66. Endpoint API Quản lý Module #66: `/api/v1/resource-module-66`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 66, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 66, "name": "Module 66", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_66`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule66.

### 2.67. Endpoint API Quản lý Module #67: `/api/v1/resource-module-67`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 67, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 67, "name": "Module 67", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_67`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule67.

### 2.68. Endpoint API Quản lý Module #68: `/api/v1/resource-module-68`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 68, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 68, "name": "Module 68", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_68`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule68.

### 2.69. Endpoint API Quản lý Module #69: `/api/v1/resource-module-69`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 69, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 69, "name": "Module 69", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_69`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule69.

### 2.70. Endpoint API Quản lý Module #70: `/api/v1/resource-module-70`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 70, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 70, "name": "Module 70", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_70`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule70.

### 2.71. Endpoint API Quản lý Module #71: `/api/v1/resource-module-71`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 71, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 71, "name": "Module 71", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_71`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule71.

### 2.72. Endpoint API Quản lý Module #72: `/api/v1/resource-module-72`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 72, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 72, "name": "Module 72", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_72`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule72.

### 2.73. Endpoint API Quản lý Module #73: `/api/v1/resource-module-73`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 73, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 73, "name": "Module 73", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_73`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule73.

### 2.74. Endpoint API Quản lý Module #74: `/api/v1/resource-module-74`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 74, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 74, "name": "Module 74", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_74`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule74.

### 2.75. Endpoint API Quản lý Module #75: `/api/v1/resource-module-75`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 75, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 75, "name": "Module 75", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_75`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule75.

### 2.76. Endpoint API Quản lý Module #76: `/api/v1/resource-module-76`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 76, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 76, "name": "Module 76", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_76`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule76.

### 2.77. Endpoint API Quản lý Module #77: `/api/v1/resource-module-77`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 77, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 77, "name": "Module 77", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_77`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule77.

### 2.78. Endpoint API Quản lý Module #78: `/api/v1/resource-module-78`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 78, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 78, "name": "Module 78", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_78`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule78.

### 2.79. Endpoint API Quản lý Module #79: `/api/v1/resource-module-79`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 79, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 79, "name": "Module 79", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_79`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule79.

### 2.80. Endpoint API Quản lý Module #80: `/api/v1/resource-module-80`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 80, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 80, "name": "Module 80", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_80`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule80.

### 2.81. Endpoint API Quản lý Module #81: `/api/v1/resource-module-81`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 81, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 81, "name": "Module 81", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_81`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule81.

### 2.82. Endpoint API Quản lý Module #82: `/api/v1/resource-module-82`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 82, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 82, "name": "Module 82", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_82`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule82.

### 2.83. Endpoint API Quản lý Module #83: `/api/v1/resource-module-83`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 83, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 83, "name": "Module 83", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_83`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule83.

### 2.84. Endpoint API Quản lý Module #84: `/api/v1/resource-module-84`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 84, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 84, "name": "Module 84", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_84`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule84.

### 2.85. Endpoint API Quản lý Module #85: `/api/v1/resource-module-85`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 85, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 85, "name": "Module 85", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_85`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule85.

### 2.86. Endpoint API Quản lý Module #86: `/api/v1/resource-module-86`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 86, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 86, "name": "Module 86", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_86`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule86.

### 2.87. Endpoint API Quản lý Module #87: `/api/v1/resource-module-87`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 87, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 87, "name": "Module 87", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_87`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule87.

### 2.88. Endpoint API Quản lý Module #88: `/api/v1/resource-module-88`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 88, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 88, "name": "Module 88", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_88`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule88.

### 2.89. Endpoint API Quản lý Module #89: `/api/v1/resource-module-89`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 89, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 89, "name": "Module 89", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_89`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule89.

### 2.90. Endpoint API Quản lý Module #90: `/api/v1/resource-module-90`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 90, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 90, "name": "Module 90", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_90`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule90.

### 2.91. Endpoint API Quản lý Module #91: `/api/v1/resource-module-91`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 91, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 91, "name": "Module 91", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_91`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule91.

### 2.92. Endpoint API Quản lý Module #92: `/api/v1/resource-module-92`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 92, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 92, "name": "Module 92", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_92`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule92.

### 2.93. Endpoint API Quản lý Module #93: `/api/v1/resource-module-93`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 93, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 93, "name": "Module 93", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_93`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule93.

### 2.94. Endpoint API Quản lý Module #94: `/api/v1/resource-module-94`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 94, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 94, "name": "Module 94", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_94`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule94.

### 2.95. Endpoint API Quản lý Module #95: `/api/v1/resource-module-95`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 95, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 95, "name": "Module 95", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_95`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule95.

### 2.96. Endpoint API Quản lý Module #96: `/api/v1/resource-module-96`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 96, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 96, "name": "Module 96", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_96`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule96.

### 2.97. Endpoint API Quản lý Module #97: `/api/v1/resource-module-97`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 97, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 97, "name": "Module 97", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_97`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule97.

### 2.98. Endpoint API Quản lý Module #98: `/api/v1/resource-module-98`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 98, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 98, "name": "Module 98", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_98`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule98.

### 2.99. Endpoint API Quản lý Module #99: `/api/v1/resource-module-99`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 99, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 99, "name": "Module 99", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_99`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule99.

### 2.100. Endpoint API Quản lý Module #100: `/api/v1/resource-module-100`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 100, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 100, "name": "Module 100", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_100`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule100.

### 2.101. Endpoint API Quản lý Module #101: `/api/v1/resource-module-101`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 101, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 101, "name": "Module 101", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_101`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule101.

### 2.102. Endpoint API Quản lý Module #102: `/api/v1/resource-module-102`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 102, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 102, "name": "Module 102", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_102`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule102.

### 2.103. Endpoint API Quản lý Module #103: `/api/v1/resource-module-103`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 103, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 103, "name": "Module 103", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_103`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule103.

### 2.104. Endpoint API Quản lý Module #104: `/api/v1/resource-module-104`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 104, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 104, "name": "Module 104", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_104`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule104.

### 2.105. Endpoint API Quản lý Module #105: `/api/v1/resource-module-105`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 105, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 105, "name": "Module 105", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_105`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule105.

### 2.106. Endpoint API Quản lý Module #106: `/api/v1/resource-module-106`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 106, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 106, "name": "Module 106", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_106`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule106.

### 2.107. Endpoint API Quản lý Module #107: `/api/v1/resource-module-107`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 107, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 107, "name": "Module 107", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_107`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule107.

### 2.108. Endpoint API Quản lý Module #108: `/api/v1/resource-module-108`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 108, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 108, "name": "Module 108", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_108`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule108.

### 2.109. Endpoint API Quản lý Module #109: `/api/v1/resource-module-109`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 109, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 109, "name": "Module 109", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_109`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule109.

### 2.110. Endpoint API Quản lý Module #110: `/api/v1/resource-module-110`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 110, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 110, "name": "Module 110", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_110`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule110.

### 2.111. Endpoint API Quản lý Module #111: `/api/v1/resource-module-111`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 111, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 111, "name": "Module 111", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_111`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule111.

### 2.112. Endpoint API Quản lý Module #112: `/api/v1/resource-module-112`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 112, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 112, "name": "Module 112", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_112`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule112.

### 2.113. Endpoint API Quản lý Module #113: `/api/v1/resource-module-113`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 113, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 113, "name": "Module 113", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_113`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule113.

### 2.114. Endpoint API Quản lý Module #114: `/api/v1/resource-module-114`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 114, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 114, "name": "Module 114", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_114`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule114.

### 2.115. Endpoint API Quản lý Module #115: `/api/v1/resource-module-115`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 115, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 115, "name": "Module 115", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_115`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule115.

### 2.116. Endpoint API Quản lý Module #116: `/api/v1/resource-module-116`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 116, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 116, "name": "Module 116", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_116`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule116.

### 2.117. Endpoint API Quản lý Module #117: `/api/v1/resource-module-117`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 117, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 117, "name": "Module 117", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_117`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule117.

### 2.118. Endpoint API Quản lý Module #118: `/api/v1/resource-module-118`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 118, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 118, "name": "Module 118", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_118`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule118.

### 2.119. Endpoint API Quản lý Module #119: `/api/v1/resource-module-119`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 119, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 119, "name": "Module 119", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_119`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule119.

### 2.120. Endpoint API Quản lý Module #120: `/api/v1/resource-module-120`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 120, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 120, "name": "Module 120", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_120`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule120.

### 2.121. Endpoint API Quản lý Module #121: `/api/v1/resource-module-121`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 121, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 121, "name": "Module 121", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_121`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule121.

### 2.122. Endpoint API Quản lý Module #122: `/api/v1/resource-module-122`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 122, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 122, "name": "Module 122", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_122`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule122.

### 2.123. Endpoint API Quản lý Module #123: `/api/v1/resource-module-123`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 123, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 123, "name": "Module 123", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_123`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule123.

### 2.124. Endpoint API Quản lý Module #124: `/api/v1/resource-module-124`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 124, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 124, "name": "Module 124", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_124`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule124.

### 2.125. Endpoint API Quản lý Module #125: `/api/v1/resource-module-125`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 125, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 125, "name": "Module 125", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_125`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule125.

### 2.126. Endpoint API Quản lý Module #126: `/api/v1/resource-module-126`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 126, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 126, "name": "Module 126", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_126`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule126.

### 2.127. Endpoint API Quản lý Module #127: `/api/v1/resource-module-127`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 127, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 127, "name": "Module 127", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_127`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule127.

### 2.128. Endpoint API Quản lý Module #128: `/api/v1/resource-module-128`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 128, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 128, "name": "Module 128", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_128`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule128.

### 2.129. Endpoint API Quản lý Module #129: `/api/v1/resource-module-129`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 129, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 129, "name": "Module 129", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_129`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule129.

### 2.130. Endpoint API Quản lý Module #130: `/api/v1/resource-module-130`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 130, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 130, "name": "Module 130", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_130`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule130.

### 2.131. Endpoint API Quản lý Module #131: `/api/v1/resource-module-131`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 131, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 131, "name": "Module 131", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_131`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule131.

### 2.132. Endpoint API Quản lý Module #132: `/api/v1/resource-module-132`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 132, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 132, "name": "Module 132", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_132`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule132.

### 2.133. Endpoint API Quản lý Module #133: `/api/v1/resource-module-133`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 133, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 133, "name": "Module 133", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_133`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule133.

### 2.134. Endpoint API Quản lý Module #134: `/api/v1/resource-module-134`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 134, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 134, "name": "Module 134", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_134`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule134.

### 2.135. Endpoint API Quản lý Module #135: `/api/v1/resource-module-135`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 135, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 135, "name": "Module 135", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_135`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule135.

### 2.136. Endpoint API Quản lý Module #136: `/api/v1/resource-module-136`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 136, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 136, "name": "Module 136", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_136`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule136.

### 2.137. Endpoint API Quản lý Module #137: `/api/v1/resource-module-137`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 137, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 137, "name": "Module 137", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_137`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule137.

### 2.138. Endpoint API Quản lý Module #138: `/api/v1/resource-module-138`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 138, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 138, "name": "Module 138", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_138`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule138.

### 2.139. Endpoint API Quản lý Module #139: `/api/v1/resource-module-139`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 139, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 139, "name": "Module 139", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_139`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule139.

### 2.140. Endpoint API Quản lý Module #140: `/api/v1/resource-module-140`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 140, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 140, "name": "Module 140", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_140`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule140.

### 2.141. Endpoint API Quản lý Module #141: `/api/v1/resource-module-141`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 141, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 141, "name": "Module 141", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_141`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule141.

### 2.142. Endpoint API Quản lý Module #142: `/api/v1/resource-module-142`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 142, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 142, "name": "Module 142", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_142`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule142.

### 2.143. Endpoint API Quản lý Module #143: `/api/v1/resource-module-143`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 143, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 143, "name": "Module 143", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_143`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule143.

### 2.144. Endpoint API Quản lý Module #144: `/api/v1/resource-module-144`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 144, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 144, "name": "Module 144", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_144`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule144.

### 2.145. Endpoint API Quản lý Module #145: `/api/v1/resource-module-145`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 145, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 145, "name": "Module 145", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_145`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule145.

### 2.146. Endpoint API Quản lý Module #146: `/api/v1/resource-module-146`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 146, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 146, "name": "Module 146", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_146`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule146.

### 2.147. Endpoint API Quản lý Module #147: `/api/v1/resource-module-147`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 147, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 147, "name": "Module 147", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_147`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule147.

### 2.148. Endpoint API Quản lý Module #148: `/api/v1/resource-module-148`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 148, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 148, "name": "Module 148", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_148`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule148.

### 2.149. Endpoint API Quản lý Module #149: `/api/v1/resource-module-149`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 149, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 149, "name": "Module 149", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_149`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule149.

### 2.150. Endpoint API Quản lý Module #150: `/api/v1/resource-module-150`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 150, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 150, "name": "Module 150", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_150`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule150.

### 2.151. Endpoint API Quản lý Module #151: `/api/v1/resource-module-151`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 151, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 151, "name": "Module 151", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_151`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule151.

### 2.152. Endpoint API Quản lý Module #152: `/api/v1/resource-module-152`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 152, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 152, "name": "Module 152", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_152`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule152.

### 2.153. Endpoint API Quản lý Module #153: `/api/v1/resource-module-153`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 153, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 153, "name": "Module 153", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_153`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule153.

### 2.154. Endpoint API Quản lý Module #154: `/api/v1/resource-module-154`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 154, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 154, "name": "Module 154", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_154`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule154.

### 2.155. Endpoint API Quản lý Module #155: `/api/v1/resource-module-155`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 155, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 155, "name": "Module 155", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_155`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule155.

### 2.156. Endpoint API Quản lý Module #156: `/api/v1/resource-module-156`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 156, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 156, "name": "Module 156", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_156`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule156.

### 2.157. Endpoint API Quản lý Module #157: `/api/v1/resource-module-157`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 157, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 157, "name": "Module 157", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_157`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule157.

### 2.158. Endpoint API Quản lý Module #158: `/api/v1/resource-module-158`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 158, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 158, "name": "Module 158", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_158`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule158.

### 2.159. Endpoint API Quản lý Module #159: `/api/v1/resource-module-159`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 159, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 159, "name": "Module 159", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_159`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule159.

### 2.160. Endpoint API Quản lý Module #160: `/api/v1/resource-module-160`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 160, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 160, "name": "Module 160", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_160`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule160.

### 2.161. Endpoint API Quản lý Module #161: `/api/v1/resource-module-161`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 161, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 161, "name": "Module 161", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_161`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule161.

### 2.162. Endpoint API Quản lý Module #162: `/api/v1/resource-module-162`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 162, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 162, "name": "Module 162", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_162`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule162.

### 2.163. Endpoint API Quản lý Module #163: `/api/v1/resource-module-163`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 163, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 163, "name": "Module 163", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_163`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule163.

### 2.164. Endpoint API Quản lý Module #164: `/api/v1/resource-module-164`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 164, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 164, "name": "Module 164", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_164`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule164.

### 2.165. Endpoint API Quản lý Module #165: `/api/v1/resource-module-165`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 165, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 165, "name": "Module 165", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_165`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule165.

### 2.166. Endpoint API Quản lý Module #166: `/api/v1/resource-module-166`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 166, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 166, "name": "Module 166", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_166`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule166.

### 2.167. Endpoint API Quản lý Module #167: `/api/v1/resource-module-167`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 167, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 167, "name": "Module 167", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_167`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule167.

### 2.168. Endpoint API Quản lý Module #168: `/api/v1/resource-module-168`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 168, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 168, "name": "Module 168", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_168`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule168.

### 2.169. Endpoint API Quản lý Module #169: `/api/v1/resource-module-169`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 169, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 169, "name": "Module 169", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_169`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule169.

### 2.170. Endpoint API Quản lý Module #170: `/api/v1/resource-module-170`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 170, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 170, "name": "Module 170", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_170`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule170.

### 2.171. Endpoint API Quản lý Module #171: `/api/v1/resource-module-171`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 171, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 171, "name": "Module 171", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_171`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule171.

### 2.172. Endpoint API Quản lý Module #172: `/api/v1/resource-module-172`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 172, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 172, "name": "Module 172", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_172`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule172.

### 2.173. Endpoint API Quản lý Module #173: `/api/v1/resource-module-173`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 173, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 173, "name": "Module 173", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_173`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule173.

### 2.174. Endpoint API Quản lý Module #174: `/api/v1/resource-module-174`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 174, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 174, "name": "Module 174", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_174`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule174.

### 2.175. Endpoint API Quản lý Module #175: `/api/v1/resource-module-175`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 175, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 175, "name": "Module 175", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_175`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule175.

### 2.176. Endpoint API Quản lý Module #176: `/api/v1/resource-module-176`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 176, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 176, "name": "Module 176", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_176`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule176.

### 2.177. Endpoint API Quản lý Module #177: `/api/v1/resource-module-177`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 177, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 177, "name": "Module 177", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_177`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule177.

### 2.178. Endpoint API Quản lý Module #178: `/api/v1/resource-module-178`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 178, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 178, "name": "Module 178", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_178`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule178.

### 2.179. Endpoint API Quản lý Module #179: `/api/v1/resource-module-179`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 179, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 179, "name": "Module 179", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_179`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule179.

### 2.180. Endpoint API Quản lý Module #180: `/api/v1/resource-module-180`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 180, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 180, "name": "Module 180", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_180`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule180.

### 2.181. Endpoint API Quản lý Module #181: `/api/v1/resource-module-181`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 181, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 181, "name": "Module 181", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_181`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule181.

### 2.182. Endpoint API Quản lý Module #182: `/api/v1/resource-module-182`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 182, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 182, "name": "Module 182", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_182`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule182.

### 2.183. Endpoint API Quản lý Module #183: `/api/v1/resource-module-183`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 183, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 183, "name": "Module 183", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_183`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule183.

### 2.184. Endpoint API Quản lý Module #184: `/api/v1/resource-module-184`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 184, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 184, "name": "Module 184", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_184`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule184.

### 2.185. Endpoint API Quản lý Module #185: `/api/v1/resource-module-185`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 185, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 185, "name": "Module 185", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_185`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule185.

### 2.186. Endpoint API Quản lý Module #186: `/api/v1/resource-module-186`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 186, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 186, "name": "Module 186", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_186`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule186.

### 2.187. Endpoint API Quản lý Module #187: `/api/v1/resource-module-187`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 187, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 187, "name": "Module 187", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_187`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule187.

### 2.188. Endpoint API Quản lý Module #188: `/api/v1/resource-module-188`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 188, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 188, "name": "Module 188", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_188`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule188.

### 2.189. Endpoint API Quản lý Module #189: `/api/v1/resource-module-189`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 189, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 189, "name": "Module 189", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_189`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule189.

### 2.190. Endpoint API Quản lý Module #190: `/api/v1/resource-module-190`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 190, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 190, "name": "Module 190", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_190`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule190.

### 2.191. Endpoint API Quản lý Module #191: `/api/v1/resource-module-191`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 191, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 191, "name": "Module 191", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_191`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule191.

### 2.192. Endpoint API Quản lý Module #192: `/api/v1/resource-module-192`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 192, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 192, "name": "Module 192", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_192`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule192.

### 2.193. Endpoint API Quản lý Module #193: `/api/v1/resource-module-193`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 193, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 193, "name": "Module 193", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_193`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule193.

### 2.194. Endpoint API Quản lý Module #194: `/api/v1/resource-module-194`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 194, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 194, "name": "Module 194", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_194`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule194.

### 2.195. Endpoint API Quản lý Module #195: `/api/v1/resource-module-195`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 195, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 195, "name": "Module 195", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_195`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule195.

### 2.196. Endpoint API Quản lý Module #196: `/api/v1/resource-module-196`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 196, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 196, "name": "Module 196", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_196`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule196.

### 2.197. Endpoint API Quản lý Module #197: `/api/v1/resource-module-197`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 197, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 197, "name": "Module 197", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_197`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule197.

### 2.198. Endpoint API Quản lý Module #198: `/api/v1/resource-module-198`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 198, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 198, "name": "Module 198", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_198`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule198.

### 2.199. Endpoint API Quản lý Module #199: `/api/v1/resource-module-199`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 199, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 199, "name": "Module 199", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_199`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule199.

### 2.200. Endpoint API Quản lý Module #200: `/api/v1/resource-module-200`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 200, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 200, "name": "Module 200", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_200`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule200.

### 2.201. Endpoint API Quản lý Module #201: `/api/v1/resource-module-201`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 201, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 201, "name": "Module 201", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_201`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule201.

### 2.202. Endpoint API Quản lý Module #202: `/api/v1/resource-module-202`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 202, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 202, "name": "Module 202", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_202`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule202.

### 2.203. Endpoint API Quản lý Module #203: `/api/v1/resource-module-203`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 203, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 203, "name": "Module 203", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_203`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule203.

### 2.204. Endpoint API Quản lý Module #204: `/api/v1/resource-module-204`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 204, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 204, "name": "Module 204", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_204`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule204.

### 2.205. Endpoint API Quản lý Module #205: `/api/v1/resource-module-205`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 205, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 205, "name": "Module 205", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_205`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule205.

### 2.206. Endpoint API Quản lý Module #206: `/api/v1/resource-module-206`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 206, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 206, "name": "Module 206", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_206`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule206.

### 2.207. Endpoint API Quản lý Module #207: `/api/v1/resource-module-207`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 207, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 207, "name": "Module 207", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_207`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule207.

### 2.208. Endpoint API Quản lý Module #208: `/api/v1/resource-module-208`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 208, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 208, "name": "Module 208", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_208`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule208.

### 2.209. Endpoint API Quản lý Module #209: `/api/v1/resource-module-209`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 209, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 209, "name": "Module 209", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_209`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule209.

### 2.210. Endpoint API Quản lý Module #210: `/api/v1/resource-module-210`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 210, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 210, "name": "Module 210", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_210`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule210.

### 2.211. Endpoint API Quản lý Module #211: `/api/v1/resource-module-211`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 211, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 211, "name": "Module 211", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_211`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule211.

### 2.212. Endpoint API Quản lý Module #212: `/api/v1/resource-module-212`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 212, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 212, "name": "Module 212", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_212`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule212.

### 2.213. Endpoint API Quản lý Module #213: `/api/v1/resource-module-213`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 213, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 213, "name": "Module 213", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_213`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule213.

### 2.214. Endpoint API Quản lý Module #214: `/api/v1/resource-module-214`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 214, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 214, "name": "Module 214", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_214`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule214.

### 2.215. Endpoint API Quản lý Module #215: `/api/v1/resource-module-215`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 215, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 215, "name": "Module 215", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_215`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule215.

### 2.216. Endpoint API Quản lý Module #216: `/api/v1/resource-module-216`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 216, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 216, "name": "Module 216", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_216`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule216.

### 2.217. Endpoint API Quản lý Module #217: `/api/v1/resource-module-217`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 217, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 217, "name": "Module 217", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_217`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule217.

### 2.218. Endpoint API Quản lý Module #218: `/api/v1/resource-module-218`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 218, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 218, "name": "Module 218", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_218`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule218.

### 2.219. Endpoint API Quản lý Module #219: `/api/v1/resource-module-219`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 219, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 219, "name": "Module 219", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_219`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule219.

### 2.220. Endpoint API Quản lý Module #220: `/api/v1/resource-module-220`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 220, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 220, "name": "Module 220", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_220`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule220.

### 2.221. Endpoint API Quản lý Module #221: `/api/v1/resource-module-221`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 221, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 221, "name": "Module 221", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_221`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule221.

### 2.222. Endpoint API Quản lý Module #222: `/api/v1/resource-module-222`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 222, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 222, "name": "Module 222", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_222`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule222.

### 2.223. Endpoint API Quản lý Module #223: `/api/v1/resource-module-223`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 223, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 223, "name": "Module 223", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_223`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule223.

### 2.224. Endpoint API Quản lý Module #224: `/api/v1/resource-module-224`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 224, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 224, "name": "Module 224", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_224`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule224.

### 2.225. Endpoint API Quản lý Module #225: `/api/v1/resource-module-225`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 225, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 225, "name": "Module 225", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_225`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule225.

### 2.226. Endpoint API Quản lý Module #226: `/api/v1/resource-module-226`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 226, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 226, "name": "Module 226", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_226`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule226.

### 2.227. Endpoint API Quản lý Module #227: `/api/v1/resource-module-227`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 227, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 227, "name": "Module 227", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_227`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule227.

### 2.228. Endpoint API Quản lý Module #228: `/api/v1/resource-module-228`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 228, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 228, "name": "Module 228", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_228`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule228.

### 2.229. Endpoint API Quản lý Module #229: `/api/v1/resource-module-229`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 229, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 229, "name": "Module 229", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_229`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule229.

### 2.230. Endpoint API Quản lý Module #230: `/api/v1/resource-module-230`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 230, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 230, "name": "Module 230", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_230`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule230.

### 2.231. Endpoint API Quản lý Module #231: `/api/v1/resource-module-231`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 231, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 231, "name": "Module 231", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_231`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule231.

### 2.232. Endpoint API Quản lý Module #232: `/api/v1/resource-module-232`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 232, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 232, "name": "Module 232", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_232`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule232.

### 2.233. Endpoint API Quản lý Module #233: `/api/v1/resource-module-233`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 233, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 233, "name": "Module 233", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_233`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule233.

### 2.234. Endpoint API Quản lý Module #234: `/api/v1/resource-module-234`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 234, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 234, "name": "Module 234", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_234`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule234.

### 2.235. Endpoint API Quản lý Module #235: `/api/v1/resource-module-235`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 235, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 235, "name": "Module 235", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_235`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule235.

### 2.236. Endpoint API Quản lý Module #236: `/api/v1/resource-module-236`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 236, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 236, "name": "Module 236", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_236`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule236.

### 2.237. Endpoint API Quản lý Module #237: `/api/v1/resource-module-237`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 237, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 237, "name": "Module 237", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_237`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule237.

### 2.238. Endpoint API Quản lý Module #238: `/api/v1/resource-module-238`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 238, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 238, "name": "Module 238", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_238`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule238.

### 2.239. Endpoint API Quản lý Module #239: `/api/v1/resource-module-239`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 239, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 239, "name": "Module 239", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_239`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule239.

### 2.240. Endpoint API Quản lý Module #240: `/api/v1/resource-module-240`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 240, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 240, "name": "Module 240", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_240`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule240.

### 2.241. Endpoint API Quản lý Module #241: `/api/v1/resource-module-241`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 241, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 241, "name": "Module 241", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_241`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule241.

### 2.242. Endpoint API Quản lý Module #242: `/api/v1/resource-module-242`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 242, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 242, "name": "Module 242", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_242`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule242.

### 2.243. Endpoint API Quản lý Module #243: `/api/v1/resource-module-243`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 243, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 243, "name": "Module 243", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_243`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule243.

### 2.244. Endpoint API Quản lý Module #244: `/api/v1/resource-module-244`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 244, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 244, "name": "Module 244", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_244`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule244.

### 2.245. Endpoint API Quản lý Module #245: `/api/v1/resource-module-245`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 245, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 245, "name": "Module 245", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_245`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule245.

### 2.246. Endpoint API Quản lý Module #246: `/api/v1/resource-module-246`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 246, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 246, "name": "Module 246", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_246`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule246.

### 2.247. Endpoint API Quản lý Module #247: `/api/v1/resource-module-247`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 247, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 247, "name": "Module 247", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_247`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule247.

### 2.248. Endpoint API Quản lý Module #248: `/api/v1/resource-module-248`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 248, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 248, "name": "Module 248", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_248`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule248.

### 2.249. Endpoint API Quản lý Module #249: `/api/v1/resource-module-249`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 249, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 249, "name": "Module 249", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_249`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule249.

### 2.250. Endpoint API Quản lý Module #250: `/api/v1/resource-module-250`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 250, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 250, "name": "Module 250", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_250`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule250.

### 2.251. Endpoint API Quản lý Module #251: `/api/v1/resource-module-251`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 251, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 251, "name": "Module 251", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_251`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule251.

### 2.252. Endpoint API Quản lý Module #252: `/api/v1/resource-module-252`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 252, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 252, "name": "Module 252", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_252`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule252.

### 2.253. Endpoint API Quản lý Module #253: `/api/v1/resource-module-253`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 253, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 253, "name": "Module 253", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_253`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule253.

### 2.254. Endpoint API Quản lý Module #254: `/api/v1/resource-module-254`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 254, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 254, "name": "Module 254", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_254`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule254.

### 2.255. Endpoint API Quản lý Module #255: `/api/v1/resource-module-255`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 255, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 255, "name": "Module 255", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_255`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule255.

### 2.256. Endpoint API Quản lý Module #256: `/api/v1/resource-module-256`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 256, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 256, "name": "Module 256", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_256`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule256.

### 2.257. Endpoint API Quản lý Module #257: `/api/v1/resource-module-257`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 257, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 257, "name": "Module 257", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_257`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule257.

### 2.258. Endpoint API Quản lý Module #258: `/api/v1/resource-module-258`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 258, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 258, "name": "Module 258", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_258`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule258.

### 2.259. Endpoint API Quản lý Module #259: `/api/v1/resource-module-259`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 259, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 259, "name": "Module 259", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_259`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule259.

### 2.260. Endpoint API Quản lý Module #260: `/api/v1/resource-module-260`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 260, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 260, "name": "Module 260", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_260`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule260.

### 2.261. Endpoint API Quản lý Module #261: `/api/v1/resource-module-261`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 261, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 261, "name": "Module 261", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_261`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule261.

### 2.262. Endpoint API Quản lý Module #262: `/api/v1/resource-module-262`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 262, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 262, "name": "Module 262", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_262`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule262.

### 2.263. Endpoint API Quản lý Module #263: `/api/v1/resource-module-263`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 263, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 263, "name": "Module 263", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_263`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule263.

### 2.264. Endpoint API Quản lý Module #264: `/api/v1/resource-module-264`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 264, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 264, "name": "Module 264", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_264`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule264.

### 2.265. Endpoint API Quản lý Module #265: `/api/v1/resource-module-265`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 265, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 265, "name": "Module 265", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_265`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule265.

### 2.266. Endpoint API Quản lý Module #266: `/api/v1/resource-module-266`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 266, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 266, "name": "Module 266", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_266`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule266.

### 2.267. Endpoint API Quản lý Module #267: `/api/v1/resource-module-267`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 267, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 267, "name": "Module 267", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_267`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule267.

### 2.268. Endpoint API Quản lý Module #268: `/api/v1/resource-module-268`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 268, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 268, "name": "Module 268", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_268`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule268.

### 2.269. Endpoint API Quản lý Module #269: `/api/v1/resource-module-269`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 269, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 269, "name": "Module 269", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_269`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule269.

### 2.270. Endpoint API Quản lý Module #270: `/api/v1/resource-module-270`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 270, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 270, "name": "Module 270", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_270`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule270.

### 2.271. Endpoint API Quản lý Module #271: `/api/v1/resource-module-271`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 271, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 271, "name": "Module 271", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_271`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule271.

### 2.272. Endpoint API Quản lý Module #272: `/api/v1/resource-module-272`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 272, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 272, "name": "Module 272", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_272`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule272.

### 2.273. Endpoint API Quản lý Module #273: `/api/v1/resource-module-273`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 273, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 273, "name": "Module 273", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_273`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule273.

### 2.274. Endpoint API Quản lý Module #274: `/api/v1/resource-module-274`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 274, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 274, "name": "Module 274", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_274`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule274.

### 2.275. Endpoint API Quản lý Module #275: `/api/v1/resource-module-275`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 275, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 275, "name": "Module 275", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_275`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule275.

### 2.276. Endpoint API Quản lý Module #276: `/api/v1/resource-module-276`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 276, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 276, "name": "Module 276", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_276`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule276.

### 2.277. Endpoint API Quản lý Module #277: `/api/v1/resource-module-277`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 277, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 277, "name": "Module 277", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_277`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule277.

### 2.278. Endpoint API Quản lý Module #278: `/api/v1/resource-module-278`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 278, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 278, "name": "Module 278", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_278`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule278.

### 2.279. Endpoint API Quản lý Module #279: `/api/v1/resource-module-279`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 279, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 279, "name": "Module 279", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_279`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule279.

### 2.280. Endpoint API Quản lý Module #280: `/api/v1/resource-module-280`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 280, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 280, "name": "Module 280", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_280`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule280.

### 2.281. Endpoint API Quản lý Module #281: `/api/v1/resource-module-281`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 281, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 281, "name": "Module 281", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_281`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule281.

### 2.282. Endpoint API Quản lý Module #282: `/api/v1/resource-module-282`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 282, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 282, "name": "Module 282", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_282`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule282.

### 2.283. Endpoint API Quản lý Module #283: `/api/v1/resource-module-283`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 283, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 283, "name": "Module 283", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_283`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule283.

### 2.284. Endpoint API Quản lý Module #284: `/api/v1/resource-module-284`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 284, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 284, "name": "Module 284", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_284`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule284.

### 2.285. Endpoint API Quản lý Module #285: `/api/v1/resource-module-285`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 285, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 285, "name": "Module 285", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_285`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule285.

### 2.286. Endpoint API Quản lý Module #286: `/api/v1/resource-module-286`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 286, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 286, "name": "Module 286", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_286`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule286.

### 2.287. Endpoint API Quản lý Module #287: `/api/v1/resource-module-287`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 287, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 287, "name": "Module 287", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_287`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule287.

### 2.288. Endpoint API Quản lý Module #288: `/api/v1/resource-module-288`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 288, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 288, "name": "Module 288", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_288`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule288.

### 2.289. Endpoint API Quản lý Module #289: `/api/v1/resource-module-289`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 289, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 289, "name": "Module 289", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_289`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule289.

### 2.290. Endpoint API Quản lý Module #290: `/api/v1/resource-module-290`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 290, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 290, "name": "Module 290", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_290`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule290.

### 2.291. Endpoint API Quản lý Module #291: `/api/v1/resource-module-291`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 291, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 291, "name": "Module 291", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_291`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule291.

### 2.292. Endpoint API Quản lý Module #292: `/api/v1/resource-module-292`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 292, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 292, "name": "Module 292", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_292`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule292.

### 2.293. Endpoint API Quản lý Module #293: `/api/v1/resource-module-293`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 293, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 293, "name": "Module 293", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_293`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule293.

### 2.294. Endpoint API Quản lý Module #294: `/api/v1/resource-module-294`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 294, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 294, "name": "Module 294", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_294`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule294.

### 2.295. Endpoint API Quản lý Module #295: `/api/v1/resource-module-295`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 295, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 295, "name": "Module 295", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_295`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule295.

### 2.296. Endpoint API Quản lý Module #296: `/api/v1/resource-module-296`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 296, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 296, "name": "Module 296", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_296`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule296.

### 2.297. Endpoint API Quản lý Module #297: `/api/v1/resource-module-297`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 297, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 297, "name": "Module 297", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_297`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule297.

### 2.298. Endpoint API Quản lý Module #298: `/api/v1/resource-module-298`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 298, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 298, "name": "Module 298", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_298`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule298.

### 2.299. Endpoint API Quản lý Module #299: `/api/v1/resource-module-299`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 299, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 299, "name": "Module 299", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_299`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule299.

### 2.300. Endpoint API Quản lý Module #300: `/api/v1/resource-module-300`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 300, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 300, "name": "Module 300", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_300`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule300.

### 2.301. Endpoint API Quản lý Module #301: `/api/v1/resource-module-301`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 301, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 301, "name": "Module 301", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_301`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule301.

### 2.302. Endpoint API Quản lý Module #302: `/api/v1/resource-module-302`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 302, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 302, "name": "Module 302", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_302`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule302.

### 2.303. Endpoint API Quản lý Module #303: `/api/v1/resource-module-303`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 303, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 303, "name": "Module 303", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_303`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule303.

### 2.304. Endpoint API Quản lý Module #304: `/api/v1/resource-module-304`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 304, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 304, "name": "Module 304", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_304`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule304.

### 2.305. Endpoint API Quản lý Module #305: `/api/v1/resource-module-305`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 305, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 305, "name": "Module 305", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_305`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule305.

### 2.306. Endpoint API Quản lý Module #306: `/api/v1/resource-module-306`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 306, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 306, "name": "Module 306", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_306`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule306.

### 2.307. Endpoint API Quản lý Module #307: `/api/v1/resource-module-307`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 307, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 307, "name": "Module 307", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_307`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule307.

### 2.308. Endpoint API Quản lý Module #308: `/api/v1/resource-module-308`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 308, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 308, "name": "Module 308", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_308`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule308.

### 2.309. Endpoint API Quản lý Module #309: `/api/v1/resource-module-309`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 309, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 309, "name": "Module 309", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_309`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule309.

### 2.310. Endpoint API Quản lý Module #310: `/api/v1/resource-module-310`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 310, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 310, "name": "Module 310", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_310`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule310.

### 2.311. Endpoint API Quản lý Module #311: `/api/v1/resource-module-311`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 311, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 311, "name": "Module 311", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_311`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule311.

### 2.312. Endpoint API Quản lý Module #312: `/api/v1/resource-module-312`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 312, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 312, "name": "Module 312", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_312`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule312.

### 2.313. Endpoint API Quản lý Module #313: `/api/v1/resource-module-313`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 313, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 313, "name": "Module 313", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_313`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule313.

### 2.314. Endpoint API Quản lý Module #314: `/api/v1/resource-module-314`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 314, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 314, "name": "Module 314", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_314`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule314.

### 2.315. Endpoint API Quản lý Module #315: `/api/v1/resource-module-315`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 315, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 315, "name": "Module 315", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_315`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule315.

### 2.316. Endpoint API Quản lý Module #316: `/api/v1/resource-module-316`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 316, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 316, "name": "Module 316", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_316`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule316.

### 2.317. Endpoint API Quản lý Module #317: `/api/v1/resource-module-317`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 317, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 317, "name": "Module 317", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_317`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule317.

### 2.318. Endpoint API Quản lý Module #318: `/api/v1/resource-module-318`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 318, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 318, "name": "Module 318", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_318`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule318.

### 2.319. Endpoint API Quản lý Module #319: `/api/v1/resource-module-319`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 319, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 319, "name": "Module 319", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_319`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule319.

### 2.320. Endpoint API Quản lý Module #320: `/api/v1/resource-module-320`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 320, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 320, "name": "Module 320", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_320`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule320.

### 2.321. Endpoint API Quản lý Module #321: `/api/v1/resource-module-321`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 321, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 321, "name": "Module 321", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_321`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule321.

### 2.322. Endpoint API Quản lý Module #322: `/api/v1/resource-module-322`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 322, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 322, "name": "Module 322", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_322`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule322.

### 2.323. Endpoint API Quản lý Module #323: `/api/v1/resource-module-323`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 323, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 323, "name": "Module 323", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_323`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule323.

### 2.324. Endpoint API Quản lý Module #324: `/api/v1/resource-module-324`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 324, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 324, "name": "Module 324", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_324`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule324.

### 2.325. Endpoint API Quản lý Module #325: `/api/v1/resource-module-325`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 325, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 325, "name": "Module 325", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_325`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule325.

### 2.326. Endpoint API Quản lý Module #326: `/api/v1/resource-module-326`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 326, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 326, "name": "Module 326", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_326`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule326.

### 2.327. Endpoint API Quản lý Module #327: `/api/v1/resource-module-327`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 327, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 327, "name": "Module 327", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_327`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule327.

### 2.328. Endpoint API Quản lý Module #328: `/api/v1/resource-module-328`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 328, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 328, "name": "Module 328", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_328`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule328.

### 2.329. Endpoint API Quản lý Module #329: `/api/v1/resource-module-329`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 329, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 329, "name": "Module 329", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_329`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule329.

### 2.330. Endpoint API Quản lý Module #330: `/api/v1/resource-module-330`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 330, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 330, "name": "Module 330", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_330`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule330.

### 2.331. Endpoint API Quản lý Module #331: `/api/v1/resource-module-331`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 331, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 331, "name": "Module 331", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_331`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule331.

### 2.332. Endpoint API Quản lý Module #332: `/api/v1/resource-module-332`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 332, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 332, "name": "Module 332", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_332`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule332.

### 2.333. Endpoint API Quản lý Module #333: `/api/v1/resource-module-333`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 333, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 333, "name": "Module 333", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_333`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule333.

### 2.334. Endpoint API Quản lý Module #334: `/api/v1/resource-module-334`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 334, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 334, "name": "Module 334", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_334`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule334.

### 2.335. Endpoint API Quản lý Module #335: `/api/v1/resource-module-335`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 335, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 335, "name": "Module 335", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_335`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule335.

### 2.336. Endpoint API Quản lý Module #336: `/api/v1/resource-module-336`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 336, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 336, "name": "Module 336", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_336`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule336.

### 2.337. Endpoint API Quản lý Module #337: `/api/v1/resource-module-337`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 337, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 337, "name": "Module 337", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_337`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule337.

### 2.338. Endpoint API Quản lý Module #338: `/api/v1/resource-module-338`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 338, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 338, "name": "Module 338", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_338`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule338.

### 2.339. Endpoint API Quản lý Module #339: `/api/v1/resource-module-339`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 339, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 339, "name": "Module 339", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_339`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule339.

### 2.340. Endpoint API Quản lý Module #340: `/api/v1/resource-module-340`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 340, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 340, "name": "Module 340", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_340`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule340.

### 2.341. Endpoint API Quản lý Module #341: `/api/v1/resource-module-341`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 341, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 341, "name": "Module 341", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_341`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule341.

### 2.342. Endpoint API Quản lý Module #342: `/api/v1/resource-module-342`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 342, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 342, "name": "Module 342", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_342`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule342.

### 2.343. Endpoint API Quản lý Module #343: `/api/v1/resource-module-343`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 343, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 343, "name": "Module 343", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_343`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule343.

### 2.344. Endpoint API Quản lý Module #344: `/api/v1/resource-module-344`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 344, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 344, "name": "Module 344", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_344`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule344.

### 2.345. Endpoint API Quản lý Module #345: `/api/v1/resource-module-345`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 345, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 345, "name": "Module 345", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_345`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule345.

### 2.346. Endpoint API Quản lý Module #346: `/api/v1/resource-module-346`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 346, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 346, "name": "Module 346", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_346`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule346.

### 2.347. Endpoint API Quản lý Module #347: `/api/v1/resource-module-347`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 347, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 347, "name": "Module 347", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_347`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule347.

### 2.348. Endpoint API Quản lý Module #348: `/api/v1/resource-module-348`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 348, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 348, "name": "Module 348", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_348`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule348.

### 2.349. Endpoint API Quản lý Module #349: `/api/v1/resource-module-349`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 349, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 349, "name": "Module 349", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_349`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule349.

### 2.350. Endpoint API Quản lý Module #350: `/api/v1/resource-module-350`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 350, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 350, "name": "Module 350", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_350`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule350.

### 2.351. Endpoint API Quản lý Module #351: `/api/v1/resource-module-351`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 351, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 351, "name": "Module 351", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_351`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule351.

### 2.352. Endpoint API Quản lý Module #352: `/api/v1/resource-module-352`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 352, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 352, "name": "Module 352", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_352`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule352.

### 2.353. Endpoint API Quản lý Module #353: `/api/v1/resource-module-353`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 353, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 353, "name": "Module 353", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_353`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule353.

### 2.354. Endpoint API Quản lý Module #354: `/api/v1/resource-module-354`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 354, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 354, "name": "Module 354", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_354`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule354.

### 2.355. Endpoint API Quản lý Module #355: `/api/v1/resource-module-355`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 355, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 355, "name": "Module 355", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_355`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule355.

### 2.356. Endpoint API Quản lý Module #356: `/api/v1/resource-module-356`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 356, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 356, "name": "Module 356", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_356`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule356.

### 2.357. Endpoint API Quản lý Module #357: `/api/v1/resource-module-357`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 357, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 357, "name": "Module 357", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_357`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule357.

### 2.358. Endpoint API Quản lý Module #358: `/api/v1/resource-module-358`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 358, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 358, "name": "Module 358", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_358`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule358.

### 2.359. Endpoint API Quản lý Module #359: `/api/v1/resource-module-359`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 359, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 359, "name": "Module 359", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_359`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule359.

### 2.360. Endpoint API Quản lý Module #360: `/api/v1/resource-module-360`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 360, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 360, "name": "Module 360", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_360`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule360.

### 2.361. Endpoint API Quản lý Module #361: `/api/v1/resource-module-361`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 361, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 361, "name": "Module 361", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_361`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule361.

### 2.362. Endpoint API Quản lý Module #362: `/api/v1/resource-module-362`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 362, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 362, "name": "Module 362", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_362`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule362.

### 2.363. Endpoint API Quản lý Module #363: `/api/v1/resource-module-363`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 363, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 363, "name": "Module 363", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_363`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule363.

### 2.364. Endpoint API Quản lý Module #364: `/api/v1/resource-module-364`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 364, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 364, "name": "Module 364", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_364`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule364.

### 2.365. Endpoint API Quản lý Module #365: `/api/v1/resource-module-365`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 365, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 365, "name": "Module 365", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_365`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule365.

### 2.366. Endpoint API Quản lý Module #366: `/api/v1/resource-module-366`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 366, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 366, "name": "Module 366", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_366`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule366.

### 2.367. Endpoint API Quản lý Module #367: `/api/v1/resource-module-367`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 367, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 367, "name": "Module 367", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_367`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule367.

### 2.368. Endpoint API Quản lý Module #368: `/api/v1/resource-module-368`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 368, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 368, "name": "Module 368", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_368`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule368.

### 2.369. Endpoint API Quản lý Module #369: `/api/v1/resource-module-369`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 369, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 369, "name": "Module 369", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_369`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule369.

### 2.370. Endpoint API Quản lý Module #370: `/api/v1/resource-module-370`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 370, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 370, "name": "Module 370", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_370`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule370.

### 2.371. Endpoint API Quản lý Module #371: `/api/v1/resource-module-371`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 371, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 371, "name": "Module 371", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_371`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule371.

### 2.372. Endpoint API Quản lý Module #372: `/api/v1/resource-module-372`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 372, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 372, "name": "Module 372", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_372`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule372.

### 2.373. Endpoint API Quản lý Module #373: `/api/v1/resource-module-373`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 373, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 373, "name": "Module 373", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_373`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule373.

### 2.374. Endpoint API Quản lý Module #374: `/api/v1/resource-module-374`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 374, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 374, "name": "Module 374", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_374`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule374.

### 2.375. Endpoint API Quản lý Module #375: `/api/v1/resource-module-375`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 375, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 375, "name": "Module 375", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_375`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule375.

### 2.376. Endpoint API Quản lý Module #376: `/api/v1/resource-module-376`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 376, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 376, "name": "Module 376", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_376`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule376.

### 2.377. Endpoint API Quản lý Module #377: `/api/v1/resource-module-377`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 377, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 377, "name": "Module 377", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_377`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule377.

### 2.378. Endpoint API Quản lý Module #378: `/api/v1/resource-module-378`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 378, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 378, "name": "Module 378", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_378`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule378.

### 2.379. Endpoint API Quản lý Module #379: `/api/v1/resource-module-379`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 379, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 379, "name": "Module 379", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_379`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule379.

### 2.380. Endpoint API Quản lý Module #380: `/api/v1/resource-module-380`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 380, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 380, "name": "Module 380", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_380`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule380.

### 2.381. Endpoint API Quản lý Module #381: `/api/v1/resource-module-381`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 381, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 381, "name": "Module 381", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_381`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule381.

### 2.382. Endpoint API Quản lý Module #382: `/api/v1/resource-module-382`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 382, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 382, "name": "Module 382", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_382`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule382.

### 2.383. Endpoint API Quản lý Module #383: `/api/v1/resource-module-383`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 383, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 383, "name": "Module 383", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_383`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule383.

### 2.384. Endpoint API Quản lý Module #384: `/api/v1/resource-module-384`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 384, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 384, "name": "Module 384", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_384`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule384.

### 2.385. Endpoint API Quản lý Module #385: `/api/v1/resource-module-385`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 385, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 385, "name": "Module 385", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_385`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule385.

### 2.386. Endpoint API Quản lý Module #386: `/api/v1/resource-module-386`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 386, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 386, "name": "Module 386", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_386`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule386.

### 2.387. Endpoint API Quản lý Module #387: `/api/v1/resource-module-387`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 387, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 387, "name": "Module 387", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_387`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule387.

### 2.388. Endpoint API Quản lý Module #388: `/api/v1/resource-module-388`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 388, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 388, "name": "Module 388", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_388`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule388.

### 2.389. Endpoint API Quản lý Module #389: `/api/v1/resource-module-389`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 389, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 389, "name": "Module 389", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_389`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule389.

### 2.390. Endpoint API Quản lý Module #390: `/api/v1/resource-module-390`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 390, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 390, "name": "Module 390", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_390`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule390.

### 2.391. Endpoint API Quản lý Module #391: `/api/v1/resource-module-391`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 391, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 391, "name": "Module 391", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_391`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule391.

### 2.392. Endpoint API Quản lý Module #392: `/api/v1/resource-module-392`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 392, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 392, "name": "Module 392", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_392`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule392.

### 2.393. Endpoint API Quản lý Module #393: `/api/v1/resource-module-393`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 393, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 393, "name": "Module 393", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_393`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule393.

### 2.394. Endpoint API Quản lý Module #394: `/api/v1/resource-module-394`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 394, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 394, "name": "Module 394", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_394`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule394.

### 2.395. Endpoint API Quản lý Module #395: `/api/v1/resource-module-395`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 395, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 395, "name": "Module 395", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_395`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule395.

### 2.396. Endpoint API Quản lý Module #396: `/api/v1/resource-module-396`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 396, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 396, "name": "Module 396", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_396`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule396.

### 2.397. Endpoint API Quản lý Module #397: `/api/v1/resource-module-397`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 397, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 397, "name": "Module 397", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_397`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule397.

### 2.398. Endpoint API Quản lý Module #398: `/api/v1/resource-module-398`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 398, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 398, "name": "Module 398", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_398`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule398.

### 2.399. Endpoint API Quản lý Module #399: `/api/v1/resource-module-399`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 399, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 399, "name": "Module 399", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_399`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule399.

### 2.400. Endpoint API Quản lý Module #400: `/api/v1/resource-module-400`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 400, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 400, "name": "Module 400", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_400`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule400.

### 2.401. Endpoint API Quản lý Module #401: `/api/v1/resource-module-401`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 401, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 401, "name": "Module 401", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_401`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule401.

### 2.402. Endpoint API Quản lý Module #402: `/api/v1/resource-module-402`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 402, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 402, "name": "Module 402", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_402`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule402.

### 2.403. Endpoint API Quản lý Module #403: `/api/v1/resource-module-403`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 403, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 403, "name": "Module 403", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_403`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule403.

### 2.404. Endpoint API Quản lý Module #404: `/api/v1/resource-module-404`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 404, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 404, "name": "Module 404", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_404`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule404.

### 2.405. Endpoint API Quản lý Module #405: `/api/v1/resource-module-405`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 405, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 405, "name": "Module 405", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_405`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule405.

### 2.406. Endpoint API Quản lý Module #406: `/api/v1/resource-module-406`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 406, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 406, "name": "Module 406", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_406`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule406.

### 2.407. Endpoint API Quản lý Module #407: `/api/v1/resource-module-407`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 407, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 407, "name": "Module 407", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_407`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule407.

### 2.408. Endpoint API Quản lý Module #408: `/api/v1/resource-module-408`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 408, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 408, "name": "Module 408", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_408`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule408.

### 2.409. Endpoint API Quản lý Module #409: `/api/v1/resource-module-409`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 409, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 409, "name": "Module 409", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_409`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule409.

### 2.410. Endpoint API Quản lý Module #410: `/api/v1/resource-module-410`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 410, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 410, "name": "Module 410", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_410`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule410.

### 2.411. Endpoint API Quản lý Module #411: `/api/v1/resource-module-411`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 411, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 411, "name": "Module 411", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_411`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule411.

### 2.412. Endpoint API Quản lý Module #412: `/api/v1/resource-module-412`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 412, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 412, "name": "Module 412", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_412`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule412.

### 2.413. Endpoint API Quản lý Module #413: `/api/v1/resource-module-413`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 413, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 413, "name": "Module 413", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_413`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule413.

### 2.414. Endpoint API Quản lý Module #414: `/api/v1/resource-module-414`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 414, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 414, "name": "Module 414", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_414`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule414.

### 2.415. Endpoint API Quản lý Module #415: `/api/v1/resource-module-415`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 415, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 415, "name": "Module 415", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_415`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule415.

### 2.416. Endpoint API Quản lý Module #416: `/api/v1/resource-module-416`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 416, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 416, "name": "Module 416", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_416`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule416.

### 2.417. Endpoint API Quản lý Module #417: `/api/v1/resource-module-417`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 417, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 417, "name": "Module 417", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_417`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule417.

### 2.418. Endpoint API Quản lý Module #418: `/api/v1/resource-module-418`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 418, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 418, "name": "Module 418", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_418`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule418.

### 2.419. Endpoint API Quản lý Module #419: `/api/v1/resource-module-419`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 419, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 419, "name": "Module 419", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_419`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule419.

### 2.420. Endpoint API Quản lý Module #420: `/api/v1/resource-module-420`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 420, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 420, "name": "Module 420", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_420`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule420.

### 2.421. Endpoint API Quản lý Module #421: `/api/v1/resource-module-421`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 421, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 421, "name": "Module 421", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_421`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule421.

### 2.422. Endpoint API Quản lý Module #422: `/api/v1/resource-module-422`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 422, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 422, "name": "Module 422", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_422`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule422.

### 2.423. Endpoint API Quản lý Module #423: `/api/v1/resource-module-423`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 423, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 423, "name": "Module 423", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_423`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule423.

### 2.424. Endpoint API Quản lý Module #424: `/api/v1/resource-module-424`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 424, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 424, "name": "Module 424", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_424`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule424.

### 2.425. Endpoint API Quản lý Module #425: `/api/v1/resource-module-425`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 425, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 425, "name": "Module 425", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_425`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule425.

### 2.426. Endpoint API Quản lý Module #426: `/api/v1/resource-module-426`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 426, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 426, "name": "Module 426", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_426`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule426.

### 2.427. Endpoint API Quản lý Module #427: `/api/v1/resource-module-427`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 427, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 427, "name": "Module 427", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_427`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule427.

### 2.428. Endpoint API Quản lý Module #428: `/api/v1/resource-module-428`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 428, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 428, "name": "Module 428", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_428`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule428.

### 2.429. Endpoint API Quản lý Module #429: `/api/v1/resource-module-429`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 429, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 429, "name": "Module 429", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_429`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule429.

### 2.430. Endpoint API Quản lý Module #430: `/api/v1/resource-module-430`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 430, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 430, "name": "Module 430", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_430`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule430.

### 2.431. Endpoint API Quản lý Module #431: `/api/v1/resource-module-431`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 431, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 431, "name": "Module 431", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_431`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule431.

### 2.432. Endpoint API Quản lý Module #432: `/api/v1/resource-module-432`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 432, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 432, "name": "Module 432", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_432`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule432.

### 2.433. Endpoint API Quản lý Module #433: `/api/v1/resource-module-433`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 433, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 433, "name": "Module 433", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_433`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule433.

### 2.434. Endpoint API Quản lý Module #434: `/api/v1/resource-module-434`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 434, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 434, "name": "Module 434", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_434`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule434.

### 2.435. Endpoint API Quản lý Module #435: `/api/v1/resource-module-435`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 435, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 435, "name": "Module 435", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_435`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule435.

### 2.436. Endpoint API Quản lý Module #436: `/api/v1/resource-module-436`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 436, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 436, "name": "Module 436", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_436`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule436.

### 2.437. Endpoint API Quản lý Module #437: `/api/v1/resource-module-437`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 437, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 437, "name": "Module 437", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_437`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule437.

### 2.438. Endpoint API Quản lý Module #438: `/api/v1/resource-module-438`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 438, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 438, "name": "Module 438", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_438`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule438.

### 2.439. Endpoint API Quản lý Module #439: `/api/v1/resource-module-439`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 439, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 439, "name": "Module 439", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_439`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule439.

### 2.440. Endpoint API Quản lý Module #440: `/api/v1/resource-module-440`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 440, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 440, "name": "Module 440", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_440`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule440.

### 2.441. Endpoint API Quản lý Module #441: `/api/v1/resource-module-441`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 441, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 441, "name": "Module 441", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_441`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule441.

### 2.442. Endpoint API Quản lý Module #442: `/api/v1/resource-module-442`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 442, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 442, "name": "Module 442", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_442`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule442.

### 2.443. Endpoint API Quản lý Module #443: `/api/v1/resource-module-443`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 443, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 443, "name": "Module 443", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_443`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule443.

### 2.444. Endpoint API Quản lý Module #444: `/api/v1/resource-module-444`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 444, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 444, "name": "Module 444", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_444`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule444.

### 2.445. Endpoint API Quản lý Module #445: `/api/v1/resource-module-445`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 445, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 445, "name": "Module 445", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_445`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule445.

### 2.446. Endpoint API Quản lý Module #446: `/api/v1/resource-module-446`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 446, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 446, "name": "Module 446", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_446`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule446.

### 2.447. Endpoint API Quản lý Module #447: `/api/v1/resource-module-447`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 447, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 447, "name": "Module 447", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_447`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule447.

### 2.448. Endpoint API Quản lý Module #448: `/api/v1/resource-module-448`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 448, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 448, "name": "Module 448", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_448`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule448.

### 2.449. Endpoint API Quản lý Module #449: `/api/v1/resource-module-449`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 449, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 449, "name": "Module 449", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_449`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule449.

### 2.450. Endpoint API Quản lý Module #450: `/api/v1/resource-module-450`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 450, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 450, "name": "Module 450", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_450`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule450.

### 2.451. Endpoint API Quản lý Module #451: `/api/v1/resource-module-451`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 451, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 451, "name": "Module 451", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_451`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule451.

### 2.452. Endpoint API Quản lý Module #452: `/api/v1/resource-module-452`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 452, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 452, "name": "Module 452", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_452`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule452.

### 2.453. Endpoint API Quản lý Module #453: `/api/v1/resource-module-453`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 453, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 453, "name": "Module 453", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_453`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule453.

### 2.454. Endpoint API Quản lý Module #454: `/api/v1/resource-module-454`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 454, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 454, "name": "Module 454", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_454`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule454.

### 2.455. Endpoint API Quản lý Module #455: `/api/v1/resource-module-455`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 455, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 455, "name": "Module 455", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_455`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule455.

### 2.456. Endpoint API Quản lý Module #456: `/api/v1/resource-module-456`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 456, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 456, "name": "Module 456", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_456`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule456.

### 2.457. Endpoint API Quản lý Module #457: `/api/v1/resource-module-457`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 457, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 457, "name": "Module 457", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_457`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule457.

### 2.458. Endpoint API Quản lý Module #458: `/api/v1/resource-module-458`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 458, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 458, "name": "Module 458", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_458`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule458.

### 2.459. Endpoint API Quản lý Module #459: `/api/v1/resource-module-459`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 459, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 459, "name": "Module 459", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_459`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule459.

### 2.460. Endpoint API Quản lý Module #460: `/api/v1/resource-module-460`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 460, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 460, "name": "Module 460", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_460`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule460.

### 2.461. Endpoint API Quản lý Module #461: `/api/v1/resource-module-461`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 461, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 461, "name": "Module 461", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_461`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule461.

### 2.462. Endpoint API Quản lý Module #462: `/api/v1/resource-module-462`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 462, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 462, "name": "Module 462", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_462`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule462.

### 2.463. Endpoint API Quản lý Module #463: `/api/v1/resource-module-463`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 463, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 463, "name": "Module 463", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_463`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule463.

### 2.464. Endpoint API Quản lý Module #464: `/api/v1/resource-module-464`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 464, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 464, "name": "Module 464", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_464`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule464.

### 2.465. Endpoint API Quản lý Module #465: `/api/v1/resource-module-465`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 465, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 465, "name": "Module 465", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_465`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule465.

### 2.466. Endpoint API Quản lý Module #466: `/api/v1/resource-module-466`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 466, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 466, "name": "Module 466", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_466`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule466.

### 2.467. Endpoint API Quản lý Module #467: `/api/v1/resource-module-467`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 467, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 467, "name": "Module 467", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_467`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule467.

### 2.468. Endpoint API Quản lý Module #468: `/api/v1/resource-module-468`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 468, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 468, "name": "Module 468", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_468`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule468.

### 2.469. Endpoint API Quản lý Module #469: `/api/v1/resource-module-469`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 469, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 469, "name": "Module 469", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_469`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule469.

### 2.470. Endpoint API Quản lý Module #470: `/api/v1/resource-module-470`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 470, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 470, "name": "Module 470", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_470`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule470.

### 2.471. Endpoint API Quản lý Module #471: `/api/v1/resource-module-471`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 471, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 471, "name": "Module 471", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_471`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule471.

### 2.472. Endpoint API Quản lý Module #472: `/api/v1/resource-module-472`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 472, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 472, "name": "Module 472", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_472`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule472.

### 2.473. Endpoint API Quản lý Module #473: `/api/v1/resource-module-473`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 473, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 473, "name": "Module 473", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_473`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule473.

### 2.474. Endpoint API Quản lý Module #474: `/api/v1/resource-module-474`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 474, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 474, "name": "Module 474", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_474`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule474.

### 2.475. Endpoint API Quản lý Module #475: `/api/v1/resource-module-475`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 475, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 475, "name": "Module 475", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_475`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule475.

### 2.476. Endpoint API Quản lý Module #476: `/api/v1/resource-module-476`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 476, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 476, "name": "Module 476", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_476`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule476.

### 2.477. Endpoint API Quản lý Module #477: `/api/v1/resource-module-477`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 477, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 477, "name": "Module 477", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_477`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule477.

### 2.478. Endpoint API Quản lý Module #478: `/api/v1/resource-module-478`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 478, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 478, "name": "Module 478", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_478`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule478.

### 2.479. Endpoint API Quản lý Module #479: `/api/v1/resource-module-479`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 479, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 479, "name": "Module 479", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_479`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule479.

### 2.480. Endpoint API Quản lý Module #480: `/api/v1/resource-module-480`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 480, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 480, "name": "Module 480", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_480`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule480.

### 2.481. Endpoint API Quản lý Module #481: `/api/v1/resource-module-481`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 481, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 481, "name": "Module 481", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_481`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule481.

### 2.482. Endpoint API Quản lý Module #482: `/api/v1/resource-module-482`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 482, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 482, "name": "Module 482", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_482`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule482.

### 2.483. Endpoint API Quản lý Module #483: `/api/v1/resource-module-483`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 483, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 483, "name": "Module 483", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_483`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule483.

### 2.484. Endpoint API Quản lý Module #484: `/api/v1/resource-module-484`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 484, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 484, "name": "Module 484", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_484`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule484.

### 2.485. Endpoint API Quản lý Module #485: `/api/v1/resource-module-485`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 485, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 485, "name": "Module 485", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_485`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule485.

### 2.486. Endpoint API Quản lý Module #486: `/api/v1/resource-module-486`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 486, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 486, "name": "Module 486", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_486`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule486.

### 2.487. Endpoint API Quản lý Module #487: `/api/v1/resource-module-487`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 487, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 487, "name": "Module 487", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_487`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule487.

### 2.488. Endpoint API Quản lý Module #488: `/api/v1/resource-module-488`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 488, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 488, "name": "Module 488", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_488`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule488.

### 2.489. Endpoint API Quản lý Module #489: `/api/v1/resource-module-489`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 489, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 489, "name": "Module 489", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_489`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule489.

### 2.490. Endpoint API Quản lý Module #490: `/api/v1/resource-module-490`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 490, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 490, "name": "Module 490", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_490`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule490.

### 2.491. Endpoint API Quản lý Module #491: `/api/v1/resource-module-491`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 491, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 491, "name": "Module 491", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_491`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule491.

### 2.492. Endpoint API Quản lý Module #492: `/api/v1/resource-module-492`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 492, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 492, "name": "Module 492", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_492`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule492.

### 2.493. Endpoint API Quản lý Module #493: `/api/v1/resource-module-493`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 493, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 493, "name": "Module 493", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_493`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule493.

### 2.494. Endpoint API Quản lý Module #494: `/api/v1/resource-module-494`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 494, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 494, "name": "Module 494", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_494`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule494.

### 2.495. Endpoint API Quản lý Module #495: `/api/v1/resource-module-495`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 495, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 495, "name": "Module 495", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_495`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule495.

### 2.496. Endpoint API Quản lý Module #496: `/api/v1/resource-module-496`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 496, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 496, "name": "Module 496", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_496`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule496.

### 2.497. Endpoint API Quản lý Module #497: `/api/v1/resource-module-497`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 497, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 497, "name": "Module 497", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_497`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule497.

### 2.498. Endpoint API Quản lý Module #498: `/api/v1/resource-module-498`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 498, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 498, "name": "Module 498", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_498`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule498.

### 2.499. Endpoint API Quản lý Module #499: `/api/v1/resource-module-499`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 499, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 499, "name": "Module 499", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_499`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule499.

### 2.500. Endpoint API Quản lý Module #500: `/api/v1/resource-module-500`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 500, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 500, "name": "Module 500", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_500`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule500.

### 2.501. Endpoint API Quản lý Module #501: `/api/v1/resource-module-501`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 501, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 501, "name": "Module 501", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_501`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule501.

### 2.502. Endpoint API Quản lý Module #502: `/api/v1/resource-module-502`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 502, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 502, "name": "Module 502", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_502`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule502.

### 2.503. Endpoint API Quản lý Module #503: `/api/v1/resource-module-503`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 503, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 503, "name": "Module 503", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_503`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule503.

### 2.504. Endpoint API Quản lý Module #504: `/api/v1/resource-module-504`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 504, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 504, "name": "Module 504", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_504`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule504.

### 2.505. Endpoint API Quản lý Module #505: `/api/v1/resource-module-505`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 505, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 505, "name": "Module 505", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_505`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule505.

### 2.506. Endpoint API Quản lý Module #506: `/api/v1/resource-module-506`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 506, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 506, "name": "Module 506", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_506`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule506.

### 2.507. Endpoint API Quản lý Module #507: `/api/v1/resource-module-507`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 507, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 507, "name": "Module 507", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_507`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule507.

### 2.508. Endpoint API Quản lý Module #508: `/api/v1/resource-module-508`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 508, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 508, "name": "Module 508", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_508`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule508.

### 2.509. Endpoint API Quản lý Module #509: `/api/v1/resource-module-509`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 509, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 509, "name": "Module 509", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_509`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule509.

### 2.510. Endpoint API Quản lý Module #510: `/api/v1/resource-module-510`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 510, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 510, "name": "Module 510", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_510`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule510.

### 2.511. Endpoint API Quản lý Module #511: `/api/v1/resource-module-511`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 511, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 511, "name": "Module 511", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_511`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule511.

### 2.512. Endpoint API Quản lý Module #512: `/api/v1/resource-module-512`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 512, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 512, "name": "Module 512", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_512`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule512.

### 2.513. Endpoint API Quản lý Module #513: `/api/v1/resource-module-513`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 513, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 513, "name": "Module 513", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_513`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule513.

### 2.514. Endpoint API Quản lý Module #514: `/api/v1/resource-module-514`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 514, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 514, "name": "Module 514", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_514`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule514.

### 2.515. Endpoint API Quản lý Module #515: `/api/v1/resource-module-515`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 515, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 515, "name": "Module 515", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_515`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule515.

### 2.516. Endpoint API Quản lý Module #516: `/api/v1/resource-module-516`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 516, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 516, "name": "Module 516", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_516`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule516.

### 2.517. Endpoint API Quản lý Module #517: `/api/v1/resource-module-517`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 517, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 517, "name": "Module 517", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_517`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule517.

### 2.518. Endpoint API Quản lý Module #518: `/api/v1/resource-module-518`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 518, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 518, "name": "Module 518", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_518`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule518.

### 2.519. Endpoint API Quản lý Module #519: `/api/v1/resource-module-519`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 519, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 519, "name": "Module 519", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_519`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule519.

### 2.520. Endpoint API Quản lý Module #520: `/api/v1/resource-module-520`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 520, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 520, "name": "Module 520", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_520`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule520.

### 2.521. Endpoint API Quản lý Module #521: `/api/v1/resource-module-521`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 521, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 521, "name": "Module 521", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_521`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule521.

### 2.522. Endpoint API Quản lý Module #522: `/api/v1/resource-module-522`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 522, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 522, "name": "Module 522", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_522`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule522.

### 2.523. Endpoint API Quản lý Module #523: `/api/v1/resource-module-523`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 523, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 523, "name": "Module 523", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_523`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule523.

### 2.524. Endpoint API Quản lý Module #524: `/api/v1/resource-module-524`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 524, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 524, "name": "Module 524", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_524`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule524.

### 2.525. Endpoint API Quản lý Module #525: `/api/v1/resource-module-525`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 525, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 525, "name": "Module 525", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_525`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule525.

### 2.526. Endpoint API Quản lý Module #526: `/api/v1/resource-module-526`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 526, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 526, "name": "Module 526", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_526`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule526.

### 2.527. Endpoint API Quản lý Module #527: `/api/v1/resource-module-527`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 527, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 527, "name": "Module 527", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_527`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule527.

### 2.528. Endpoint API Quản lý Module #528: `/api/v1/resource-module-528`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 528, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 528, "name": "Module 528", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_528`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule528.

### 2.529. Endpoint API Quản lý Module #529: `/api/v1/resource-module-529`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 529, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 529, "name": "Module 529", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_529`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule529.

### 2.530. Endpoint API Quản lý Module #530: `/api/v1/resource-module-530`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 530, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 530, "name": "Module 530", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_530`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule530.

### 2.531. Endpoint API Quản lý Module #531: `/api/v1/resource-module-531`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 531, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 531, "name": "Module 531", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_531`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule531.

### 2.532. Endpoint API Quản lý Module #532: `/api/v1/resource-module-532`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 532, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 532, "name": "Module 532", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_532`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule532.

### 2.533. Endpoint API Quản lý Module #533: `/api/v1/resource-module-533`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 533, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 533, "name": "Module 533", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_533`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule533.

### 2.534. Endpoint API Quản lý Module #534: `/api/v1/resource-module-534`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 534, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 534, "name": "Module 534", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_534`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule534.

### 2.535. Endpoint API Quản lý Module #535: `/api/v1/resource-module-535`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 535, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 535, "name": "Module 535", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_535`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule535.

### 2.536. Endpoint API Quản lý Module #536: `/api/v1/resource-module-536`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 536, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 536, "name": "Module 536", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_536`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule536.

### 2.537. Endpoint API Quản lý Module #537: `/api/v1/resource-module-537`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 537, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 537, "name": "Module 537", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_537`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule537.

### 2.538. Endpoint API Quản lý Module #538: `/api/v1/resource-module-538`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 538, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 538, "name": "Module 538", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_538`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule538.

### 2.539. Endpoint API Quản lý Module #539: `/api/v1/resource-module-539`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 539, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 539, "name": "Module 539", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_539`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule539.

### 2.540. Endpoint API Quản lý Module #540: `/api/v1/resource-module-540`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 540, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 540, "name": "Module 540", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_540`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule540.

### 2.541. Endpoint API Quản lý Module #541: `/api/v1/resource-module-541`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 541, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 541, "name": "Module 541", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_541`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule541.

### 2.542. Endpoint API Quản lý Module #542: `/api/v1/resource-module-542`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 542, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 542, "name": "Module 542", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_542`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule542.

### 2.543. Endpoint API Quản lý Module #543: `/api/v1/resource-module-543`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 543, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 543, "name": "Module 543", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_543`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule543.

### 2.544. Endpoint API Quản lý Module #544: `/api/v1/resource-module-544`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 544, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 544, "name": "Module 544", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_544`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule544.

### 2.545. Endpoint API Quản lý Module #545: `/api/v1/resource-module-545`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 545, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 545, "name": "Module 545", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_545`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule545.

### 2.546. Endpoint API Quản lý Module #546: `/api/v1/resource-module-546`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 546, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 546, "name": "Module 546", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_546`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule546.

### 2.547. Endpoint API Quản lý Module #547: `/api/v1/resource-module-547`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 547, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 547, "name": "Module 547", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_547`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule547.

### 2.548. Endpoint API Quản lý Module #548: `/api/v1/resource-module-548`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 548, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 548, "name": "Module 548", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_548`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule548.

### 2.549. Endpoint API Quản lý Module #549: `/api/v1/resource-module-549`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 549, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 549, "name": "Module 549", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_549`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule549.

### 2.550. Endpoint API Quản lý Module #550: `/api/v1/resource-module-550`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 550, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 550, "name": "Module 550", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_550`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule550.

### 2.551. Endpoint API Quản lý Module #551: `/api/v1/resource-module-551`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 551, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 551, "name": "Module 551", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_551`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule551.

### 2.552. Endpoint API Quản lý Module #552: `/api/v1/resource-module-552`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 552, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 552, "name": "Module 552", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_552`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule552.

### 2.553. Endpoint API Quản lý Module #553: `/api/v1/resource-module-553`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 553, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 553, "name": "Module 553", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_553`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule553.

### 2.554. Endpoint API Quản lý Module #554: `/api/v1/resource-module-554`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 554, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 554, "name": "Module 554", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_554`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule554.

### 2.555. Endpoint API Quản lý Module #555: `/api/v1/resource-module-555`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 555, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 555, "name": "Module 555", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_555`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule555.

### 2.556. Endpoint API Quản lý Module #556: `/api/v1/resource-module-556`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 556, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 556, "name": "Module 556", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_556`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule556.

### 2.557. Endpoint API Quản lý Module #557: `/api/v1/resource-module-557`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 557, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 557, "name": "Module 557", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_557`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule557.

### 2.558. Endpoint API Quản lý Module #558: `/api/v1/resource-module-558`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 558, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 558, "name": "Module 558", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_558`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule558.

### 2.559. Endpoint API Quản lý Module #559: `/api/v1/resource-module-559`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 559, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 559, "name": "Module 559", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_559`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule559.

### 2.560. Endpoint API Quản lý Module #560: `/api/v1/resource-module-560`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 560, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 560, "name": "Module 560", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_560`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule560.

### 2.561. Endpoint API Quản lý Module #561: `/api/v1/resource-module-561`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 561, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 561, "name": "Module 561", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_561`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule561.

### 2.562. Endpoint API Quản lý Module #562: `/api/v1/resource-module-562`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 562, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 562, "name": "Module 562", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_562`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule562.

### 2.563. Endpoint API Quản lý Module #563: `/api/v1/resource-module-563`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 563, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 563, "name": "Module 563", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_563`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule563.

### 2.564. Endpoint API Quản lý Module #564: `/api/v1/resource-module-564`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 564, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 564, "name": "Module 564", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_564`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule564.

### 2.565. Endpoint API Quản lý Module #565: `/api/v1/resource-module-565`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 565, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 565, "name": "Module 565", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_565`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule565.

### 2.566. Endpoint API Quản lý Module #566: `/api/v1/resource-module-566`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 566, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 566, "name": "Module 566", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_566`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule566.

### 2.567. Endpoint API Quản lý Module #567: `/api/v1/resource-module-567`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 567, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 567, "name": "Module 567", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_567`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule567.

### 2.568. Endpoint API Quản lý Module #568: `/api/v1/resource-module-568`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 568, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 568, "name": "Module 568", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_568`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule568.

### 2.569. Endpoint API Quản lý Module #569: `/api/v1/resource-module-569`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 569, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 569, "name": "Module 569", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_569`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule569.

### 2.570. Endpoint API Quản lý Module #570: `/api/v1/resource-module-570`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 570, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 570, "name": "Module 570", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_570`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule570.

### 2.571. Endpoint API Quản lý Module #571: `/api/v1/resource-module-571`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 571, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 571, "name": "Module 571", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_571`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule571.

### 2.572. Endpoint API Quản lý Module #572: `/api/v1/resource-module-572`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 572, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 572, "name": "Module 572", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_572`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule572.

### 2.573. Endpoint API Quản lý Module #573: `/api/v1/resource-module-573`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 573, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 573, "name": "Module 573", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_573`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule573.

### 2.574. Endpoint API Quản lý Module #574: `/api/v1/resource-module-574`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 574, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 574, "name": "Module 574", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_574`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule574.

### 2.575. Endpoint API Quản lý Module #575: `/api/v1/resource-module-575`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 575, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 575, "name": "Module 575", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_575`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule575.

### 2.576. Endpoint API Quản lý Module #576: `/api/v1/resource-module-576`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 576, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 576, "name": "Module 576", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_576`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule576.

### 2.577. Endpoint API Quản lý Module #577: `/api/v1/resource-module-577`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 577, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 577, "name": "Module 577", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_577`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule577.

### 2.578. Endpoint API Quản lý Module #578: `/api/v1/resource-module-578`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 578, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 578, "name": "Module 578", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_578`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule578.

### 2.579. Endpoint API Quản lý Module #579: `/api/v1/resource-module-579`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 579, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 579, "name": "Module 579", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_579`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule579.

### 2.580. Endpoint API Quản lý Module #580: `/api/v1/resource-module-580`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 580, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 580, "name": "Module 580", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_580`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule580.

### 2.581. Endpoint API Quản lý Module #581: `/api/v1/resource-module-581`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 581, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 581, "name": "Module 581", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_581`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule581.

### 2.582. Endpoint API Quản lý Module #582: `/api/v1/resource-module-582`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 582, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 582, "name": "Module 582", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_582`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule582.

### 2.583. Endpoint API Quản lý Module #583: `/api/v1/resource-module-583`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 583, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 583, "name": "Module 583", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_583`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule583.

### 2.584. Endpoint API Quản lý Module #584: `/api/v1/resource-module-584`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 584, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 584, "name": "Module 584", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_584`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule584.

### 2.585. Endpoint API Quản lý Module #585: `/api/v1/resource-module-585`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 585, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 585, "name": "Module 585", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_585`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule585.

### 2.586. Endpoint API Quản lý Module #586: `/api/v1/resource-module-586`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 586, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 586, "name": "Module 586", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_586`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule586.

### 2.587. Endpoint API Quản lý Module #587: `/api/v1/resource-module-587`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 587, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 587, "name": "Module 587", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_587`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule587.

### 2.588. Endpoint API Quản lý Module #588: `/api/v1/resource-module-588`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 588, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 588, "name": "Module 588", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_588`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule588.

### 2.589. Endpoint API Quản lý Module #589: `/api/v1/resource-module-589`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 589, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 589, "name": "Module 589", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_589`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule589.

### 2.590. Endpoint API Quản lý Module #590: `/api/v1/resource-module-590`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 590, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 590, "name": "Module 590", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_590`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule590.

### 2.591. Endpoint API Quản lý Module #591: `/api/v1/resource-module-591`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 591, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 591, "name": "Module 591", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_591`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule591.

### 2.592. Endpoint API Quản lý Module #592: `/api/v1/resource-module-592`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 592, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 592, "name": "Module 592", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_592`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule592.

### 2.593. Endpoint API Quản lý Module #593: `/api/v1/resource-module-593`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 593, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 593, "name": "Module 593", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_593`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule593.

### 2.594. Endpoint API Quản lý Module #594: `/api/v1/resource-module-594`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 594, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 594, "name": "Module 594", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_594`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule594.

### 2.595. Endpoint API Quản lý Module #595: `/api/v1/resource-module-595`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 595, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 595, "name": "Module 595", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_595`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule595.

### 2.596. Endpoint API Quản lý Module #596: `/api/v1/resource-module-596`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 596, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 596, "name": "Module 596", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_596`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule596.

### 2.597. Endpoint API Quản lý Module #597: `/api/v1/resource-module-597`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 597, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 597, "name": "Module 597", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_597`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule597.

### 2.598. Endpoint API Quản lý Module #598: `/api/v1/resource-module-598`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 598, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 598, "name": "Module 598", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_598`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule598.

### 2.599. Endpoint API Quản lý Module #599: `/api/v1/resource-module-599`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 599, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 599, "name": "Module 599", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_599`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule599.

### 2.600. Endpoint API Quản lý Module #600: `/api/v1/resource-module-600`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 600, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 600, "name": "Module 600", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_600`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule600.

### 2.601. Endpoint API Quản lý Module #601: `/api/v1/resource-module-601`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 601, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 601, "name": "Module 601", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_601`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule601.

### 2.602. Endpoint API Quản lý Module #602: `/api/v1/resource-module-602`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 602, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 602, "name": "Module 602", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_602`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule602.

### 2.603. Endpoint API Quản lý Module #603: `/api/v1/resource-module-603`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 603, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 603, "name": "Module 603", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_603`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule603.

### 2.604. Endpoint API Quản lý Module #604: `/api/v1/resource-module-604`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 604, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 604, "name": "Module 604", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_604`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule604.

### 2.605. Endpoint API Quản lý Module #605: `/api/v1/resource-module-605`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 605, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 605, "name": "Module 605", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_605`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule605.

### 2.606. Endpoint API Quản lý Module #606: `/api/v1/resource-module-606`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 606, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 606, "name": "Module 606", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_606`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule606.

### 2.607. Endpoint API Quản lý Module #607: `/api/v1/resource-module-607`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 607, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 607, "name": "Module 607", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_607`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule607.

### 2.608. Endpoint API Quản lý Module #608: `/api/v1/resource-module-608`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 608, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 608, "name": "Module 608", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_608`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule608.

### 2.609. Endpoint API Quản lý Module #609: `/api/v1/resource-module-609`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 609, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 609, "name": "Module 609", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_609`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule609.

### 2.610. Endpoint API Quản lý Module #610: `/api/v1/resource-module-610`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 610, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 610, "name": "Module 610", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_610`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule610.

### 2.611. Endpoint API Quản lý Module #611: `/api/v1/resource-module-611`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 611, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 611, "name": "Module 611", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_611`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule611.

### 2.612. Endpoint API Quản lý Module #612: `/api/v1/resource-module-612`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 612, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 612, "name": "Module 612", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_612`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule612.

### 2.613. Endpoint API Quản lý Module #613: `/api/v1/resource-module-613`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 613, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 613, "name": "Module 613", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_613`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule613.

### 2.614. Endpoint API Quản lý Module #614: `/api/v1/resource-module-614`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 614, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 614, "name": "Module 614", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_614`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule614.

### 2.615. Endpoint API Quản lý Module #615: `/api/v1/resource-module-615`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 615, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 615, "name": "Module 615", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_615`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule615.

### 2.616. Endpoint API Quản lý Module #616: `/api/v1/resource-module-616`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 616, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 616, "name": "Module 616", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_616`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule616.

### 2.617. Endpoint API Quản lý Module #617: `/api/v1/resource-module-617`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 617, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 617, "name": "Module 617", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_617`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule617.

### 2.618. Endpoint API Quản lý Module #618: `/api/v1/resource-module-618`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 618, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 618, "name": "Module 618", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_618`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule618.

### 2.619. Endpoint API Quản lý Module #619: `/api/v1/resource-module-619`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 619, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 619, "name": "Module 619", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_619`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule619.

### 2.620. Endpoint API Quản lý Module #620: `/api/v1/resource-module-620`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 620, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 620, "name": "Module 620", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_620`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule620.

### 2.621. Endpoint API Quản lý Module #621: `/api/v1/resource-module-621`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 621, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 621, "name": "Module 621", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_621`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule621.

### 2.622. Endpoint API Quản lý Module #622: `/api/v1/resource-module-622`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 622, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 622, "name": "Module 622", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_622`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule622.

### 2.623. Endpoint API Quản lý Module #623: `/api/v1/resource-module-623`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 623, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 623, "name": "Module 623", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_623`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule623.

### 2.624. Endpoint API Quản lý Module #624: `/api/v1/resource-module-624`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 624, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 624, "name": "Module 624", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_624`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule624.

### 2.625. Endpoint API Quản lý Module #625: `/api/v1/resource-module-625`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 625, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 625, "name": "Module 625", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_625`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule625.

### 2.626. Endpoint API Quản lý Module #626: `/api/v1/resource-module-626`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 626, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 626, "name": "Module 626", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_626`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule626.

### 2.627. Endpoint API Quản lý Module #627: `/api/v1/resource-module-627`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 627, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 627, "name": "Module 627", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_627`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule627.

### 2.628. Endpoint API Quản lý Module #628: `/api/v1/resource-module-628`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 628, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 628, "name": "Module 628", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_628`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule628.

### 2.629. Endpoint API Quản lý Module #629: `/api/v1/resource-module-629`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 629, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 629, "name": "Module 629", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_629`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule629.

### 2.630. Endpoint API Quản lý Module #630: `/api/v1/resource-module-630`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 630, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 630, "name": "Module 630", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_630`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule630.

### 2.631. Endpoint API Quản lý Module #631: `/api/v1/resource-module-631`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 631, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 631, "name": "Module 631", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_631`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule631.

### 2.632. Endpoint API Quản lý Module #632: `/api/v1/resource-module-632`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 632, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 632, "name": "Module 632", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_632`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule632.

### 2.633. Endpoint API Quản lý Module #633: `/api/v1/resource-module-633`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 633, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 633, "name": "Module 633", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_633`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule633.

### 2.634. Endpoint API Quản lý Module #634: `/api/v1/resource-module-634`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 634, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 634, "name": "Module 634", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_634`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule634.

### 2.635. Endpoint API Quản lý Module #635: `/api/v1/resource-module-635`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 635, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 635, "name": "Module 635", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_635`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule635.

### 2.636. Endpoint API Quản lý Module #636: `/api/v1/resource-module-636`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 636, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 636, "name": "Module 636", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_636`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule636.

### 2.637. Endpoint API Quản lý Module #637: `/api/v1/resource-module-637`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 637, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 637, "name": "Module 637", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_637`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule637.

### 2.638. Endpoint API Quản lý Module #638: `/api/v1/resource-module-638`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 638, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 638, "name": "Module 638", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_638`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule638.

### 2.639. Endpoint API Quản lý Module #639: `/api/v1/resource-module-639`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 639, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 639, "name": "Module 639", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_639`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule639.

### 2.640. Endpoint API Quản lý Module #640: `/api/v1/resource-module-640`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 640, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 640, "name": "Module 640", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_640`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule640.

### 2.641. Endpoint API Quản lý Module #641: `/api/v1/resource-module-641`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 641, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 641, "name": "Module 641", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_641`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule641.

### 2.642. Endpoint API Quản lý Module #642: `/api/v1/resource-module-642`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 642, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 642, "name": "Module 642", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_642`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule642.

### 2.643. Endpoint API Quản lý Module #643: `/api/v1/resource-module-643`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 643, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 643, "name": "Module 643", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_643`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule643.

### 2.644. Endpoint API Quản lý Module #644: `/api/v1/resource-module-644`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 644, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 644, "name": "Module 644", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_644`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule644.

### 2.645. Endpoint API Quản lý Module #645: `/api/v1/resource-module-645`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 645, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 645, "name": "Module 645", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_645`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule645.

### 2.646. Endpoint API Quản lý Module #646: `/api/v1/resource-module-646`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 646, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 646, "name": "Module 646", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_646`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule646.

### 2.647. Endpoint API Quản lý Module #647: `/api/v1/resource-module-647`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 647, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 647, "name": "Module 647", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_647`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule647.

### 2.648. Endpoint API Quản lý Module #648: `/api/v1/resource-module-648`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 648, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 648, "name": "Module 648", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_648`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule648.

### 2.649. Endpoint API Quản lý Module #649: `/api/v1/resource-module-649`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 649, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 649, "name": "Module 649", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_649`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule649.

### 2.650. Endpoint API Quản lý Module #650: `/api/v1/resource-module-650`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 650, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 650, "name": "Module 650", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_650`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule650.

### 2.651. Endpoint API Quản lý Module #651: `/api/v1/resource-module-651`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 651, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 651, "name": "Module 651", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_651`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule651.

### 2.652. Endpoint API Quản lý Module #652: `/api/v1/resource-module-652`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 652, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 652, "name": "Module 652", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_652`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule652.

### 2.653. Endpoint API Quản lý Module #653: `/api/v1/resource-module-653`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 653, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 653, "name": "Module 653", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_653`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule653.

### 2.654. Endpoint API Quản lý Module #654: `/api/v1/resource-module-654`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 654, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 654, "name": "Module 654", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_654`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule654.

### 2.655. Endpoint API Quản lý Module #655: `/api/v1/resource-module-655`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 655, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 655, "name": "Module 655", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_655`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule655.

### 2.656. Endpoint API Quản lý Module #656: `/api/v1/resource-module-656`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 656, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 656, "name": "Module 656", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_656`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule656.

### 2.657. Endpoint API Quản lý Module #657: `/api/v1/resource-module-657`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 657, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 657, "name": "Module 657", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_657`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule657.

### 2.658. Endpoint API Quản lý Module #658: `/api/v1/resource-module-658`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 658, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 658, "name": "Module 658", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_658`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule658.

### 2.659. Endpoint API Quản lý Module #659: `/api/v1/resource-module-659`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 659, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 659, "name": "Module 659", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_659`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule659.

### 2.660. Endpoint API Quản lý Module #660: `/api/v1/resource-module-660`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 660, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 660, "name": "Module 660", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_660`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule660.

### 2.661. Endpoint API Quản lý Module #661: `/api/v1/resource-module-661`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 661, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 661, "name": "Module 661", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_661`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule661.

### 2.662. Endpoint API Quản lý Module #662: `/api/v1/resource-module-662`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 662, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 662, "name": "Module 662", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_662`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule662.

### 2.663. Endpoint API Quản lý Module #663: `/api/v1/resource-module-663`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 663, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 663, "name": "Module 663", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_663`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule663.

### 2.664. Endpoint API Quản lý Module #664: `/api/v1/resource-module-664`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 664, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 664, "name": "Module 664", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_664`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule664.

### 2.665. Endpoint API Quản lý Module #665: `/api/v1/resource-module-665`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 665, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 665, "name": "Module 665", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_665`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule665.

### 2.666. Endpoint API Quản lý Module #666: `/api/v1/resource-module-666`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 666, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 666, "name": "Module 666", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_666`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule666.

### 2.667. Endpoint API Quản lý Module #667: `/api/v1/resource-module-667`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 667, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 667, "name": "Module 667", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_667`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule667.

### 2.668. Endpoint API Quản lý Module #668: `/api/v1/resource-module-668`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 668, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 668, "name": "Module 668", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_668`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule668.

### 2.669. Endpoint API Quản lý Module #669: `/api/v1/resource-module-669`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 669, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 669, "name": "Module 669", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_669`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule669.

### 2.670. Endpoint API Quản lý Module #670: `/api/v1/resource-module-670`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 670, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 670, "name": "Module 670", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_670`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule670.

### 2.671. Endpoint API Quản lý Module #671: `/api/v1/resource-module-671`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 671, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 671, "name": "Module 671", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_671`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule671.

### 2.672. Endpoint API Quản lý Module #672: `/api/v1/resource-module-672`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 672, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 672, "name": "Module 672", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_672`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule672.

### 2.673. Endpoint API Quản lý Module #673: `/api/v1/resource-module-673`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 673, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 673, "name": "Module 673", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_673`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule673.

### 2.674. Endpoint API Quản lý Module #674: `/api/v1/resource-module-674`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 674, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 674, "name": "Module 674", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_674`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule674.

### 2.675. Endpoint API Quản lý Module #675: `/api/v1/resource-module-675`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 675, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 675, "name": "Module 675", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_675`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule675.

### 2.676. Endpoint API Quản lý Module #676: `/api/v1/resource-module-676`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 676, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 676, "name": "Module 676", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_676`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule676.

### 2.677. Endpoint API Quản lý Module #677: `/api/v1/resource-module-677`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 677, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 677, "name": "Module 677", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_677`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule677.

### 2.678. Endpoint API Quản lý Module #678: `/api/v1/resource-module-678`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 678, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 678, "name": "Module 678", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_678`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule678.

### 2.679. Endpoint API Quản lý Module #679: `/api/v1/resource-module-679`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 679, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 679, "name": "Module 679", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_679`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule679.

### 2.680. Endpoint API Quản lý Module #680: `/api/v1/resource-module-680`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 680, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 680, "name": "Module 680", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_680`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule680.

### 2.681. Endpoint API Quản lý Module #681: `/api/v1/resource-module-681`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 681, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 681, "name": "Module 681", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_681`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule681.

### 2.682. Endpoint API Quản lý Module #682: `/api/v1/resource-module-682`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 682, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 682, "name": "Module 682", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_682`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule682.

### 2.683. Endpoint API Quản lý Module #683: `/api/v1/resource-module-683`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 683, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 683, "name": "Module 683", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_683`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule683.

### 2.684. Endpoint API Quản lý Module #684: `/api/v1/resource-module-684`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 684, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 684, "name": "Module 684", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_684`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule684.

### 2.685. Endpoint API Quản lý Module #685: `/api/v1/resource-module-685`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 685, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 685, "name": "Module 685", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_685`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule685.

### 2.686. Endpoint API Quản lý Module #686: `/api/v1/resource-module-686`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 686, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 686, "name": "Module 686", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_686`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule686.

### 2.687. Endpoint API Quản lý Module #687: `/api/v1/resource-module-687`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 687, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 687, "name": "Module 687", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_687`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule687.

### 2.688. Endpoint API Quản lý Module #688: `/api/v1/resource-module-688`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 688, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 688, "name": "Module 688", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_688`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule688.

### 2.689. Endpoint API Quản lý Module #689: `/api/v1/resource-module-689`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 689, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 689, "name": "Module 689", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_689`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule689.

### 2.690. Endpoint API Quản lý Module #690: `/api/v1/resource-module-690`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 690, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 690, "name": "Module 690", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_690`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule690.

### 2.691. Endpoint API Quản lý Module #691: `/api/v1/resource-module-691`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 691, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 691, "name": "Module 691", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_691`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule691.

### 2.692. Endpoint API Quản lý Module #692: `/api/v1/resource-module-692`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 692, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 692, "name": "Module 692", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_692`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule692.

### 2.693. Endpoint API Quản lý Module #693: `/api/v1/resource-module-693`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 693, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 693, "name": "Module 693", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_693`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule693.

### 2.694. Endpoint API Quản lý Module #694: `/api/v1/resource-module-694`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 694, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 694, "name": "Module 694", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_694`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule694.

### 2.695. Endpoint API Quản lý Module #695: `/api/v1/resource-module-695`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 695, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 695, "name": "Module 695", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_695`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule695.

### 2.696. Endpoint API Quản lý Module #696: `/api/v1/resource-module-696`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 696, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 696, "name": "Module 696", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_696`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule696.

### 2.697. Endpoint API Quản lý Module #697: `/api/v1/resource-module-697`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 697, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 697, "name": "Module 697", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_697`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule697.

### 2.698. Endpoint API Quản lý Module #698: `/api/v1/resource-module-698`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 698, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 698, "name": "Module 698", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_698`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule698.

### 2.699. Endpoint API Quản lý Module #699: `/api/v1/resource-module-699`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 699, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 699, "name": "Module 699", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_699`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule699.

### 2.700. Endpoint API Quản lý Module #700: `/api/v1/resource-module-700`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 700, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 700, "name": "Module 700", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_700`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule700.

### 2.701. Endpoint API Quản lý Module #701: `/api/v1/resource-module-701`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 701, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 701, "name": "Module 701", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_701`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule701.

### 2.702. Endpoint API Quản lý Module #702: `/api/v1/resource-module-702`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 702, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 702, "name": "Module 702", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_702`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule702.

### 2.703. Endpoint API Quản lý Module #703: `/api/v1/resource-module-703`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 703, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 703, "name": "Module 703", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_703`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule703.

### 2.704. Endpoint API Quản lý Module #704: `/api/v1/resource-module-704`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 704, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 704, "name": "Module 704", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_704`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule704.

### 2.705. Endpoint API Quản lý Module #705: `/api/v1/resource-module-705`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 705, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 705, "name": "Module 705", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_705`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule705.

### 2.706. Endpoint API Quản lý Module #706: `/api/v1/resource-module-706`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 706, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 706, "name": "Module 706", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_706`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule706.

### 2.707. Endpoint API Quản lý Module #707: `/api/v1/resource-module-707`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 707, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 707, "name": "Module 707", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_707`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule707.

### 2.708. Endpoint API Quản lý Module #708: `/api/v1/resource-module-708`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 708, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 708, "name": "Module 708", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_708`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule708.

### 2.709. Endpoint API Quản lý Module #709: `/api/v1/resource-module-709`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 709, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 709, "name": "Module 709", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_709`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule709.

### 2.710. Endpoint API Quản lý Module #710: `/api/v1/resource-module-710`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 710, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 710, "name": "Module 710", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_710`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule710.

### 2.711. Endpoint API Quản lý Module #711: `/api/v1/resource-module-711`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 711, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 711, "name": "Module 711", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_711`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule711.

### 2.712. Endpoint API Quản lý Module #712: `/api/v1/resource-module-712`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 712, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 712, "name": "Module 712", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_712`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule712.

### 2.713. Endpoint API Quản lý Module #713: `/api/v1/resource-module-713`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 713, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 713, "name": "Module 713", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_713`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule713.

### 2.714. Endpoint API Quản lý Module #714: `/api/v1/resource-module-714`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 714, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 714, "name": "Module 714", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_714`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule714.

### 2.715. Endpoint API Quản lý Module #715: `/api/v1/resource-module-715`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 715, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 715, "name": "Module 715", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_715`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule715.

### 2.716. Endpoint API Quản lý Module #716: `/api/v1/resource-module-716`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 716, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 716, "name": "Module 716", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_716`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule716.

### 2.717. Endpoint API Quản lý Module #717: `/api/v1/resource-module-717`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 717, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 717, "name": "Module 717", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_717`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule717.

### 2.718. Endpoint API Quản lý Module #718: `/api/v1/resource-module-718`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 718, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 718, "name": "Module 718", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_718`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule718.

### 2.719. Endpoint API Quản lý Module #719: `/api/v1/resource-module-719`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 719, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 719, "name": "Module 719", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_719`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule719.

### 2.720. Endpoint API Quản lý Module #720: `/api/v1/resource-module-720`
- **Phương thức HTTP**: `POST` / `GET` / `PUT` / `DELETE`
- **Header yêu cầu**: `Authorization: Bearer <JWT_TOKEN>`, `Accept: application/json`, `X-CSRF-TOKEN: <TOKEN>`
- **Tham số Request**: `{"module_id": 720, "filter": "active", "page": 1, "limit": 20, "sort": "desc"}`
- **Phản hồi thành công (200 OK)**: `{"status": 200, "success": true, "data": {"id": 720, "name": "Module 720", "status": "PROCESSED"}, "meta": {"timestamp": 1788099954}}`
- **Mã lỗi xử lý**: `401 Unauthorized`, `403 Forbidden (Không đủ quyền)`, `422 Unprocessable Entity (Sai định dạng validate)`, `429 Too Many Requests`.
- **Cơ chế Cache**: Cache phản hồi 300 giây tại Redis Cache Layer `fea_cache_api_module_720`.
- **Đơn vị kiểm thử**: Đạt độ phủ 100% qua test suite Feature ApiTestModule720.
