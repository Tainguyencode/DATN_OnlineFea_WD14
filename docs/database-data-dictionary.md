# TỪ ĐIỂN DỮ LIỆU VÀ ĐẶC TẢ CƠ SỞ DỮ LIỆU HỆ THỐNG DATN ONLINEFEA

## 1. MÔ TẢ CẤU TRÚC VÀ CÁC RÀNG BUỘC TOÀN VẸN CƠ SỞ DỮ LIỆU

### 3.1. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_1
- **Tên bảng vật lý**: `fea_data_schema_table_1`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_1_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_1_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.2. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_2
- **Tên bảng vật lý**: `fea_data_schema_table_2`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_2_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_2_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.3. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_3
- **Tên bảng vật lý**: `fea_data_schema_table_3`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_3_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_3_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.4. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_4
- **Tên bảng vật lý**: `fea_data_schema_table_4`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_4_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_4_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.5. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_5
- **Tên bảng vật lý**: `fea_data_schema_table_5`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_5_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_5_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.6. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_6
- **Tên bảng vật lý**: `fea_data_schema_table_6`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_6_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_6_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.7. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_7
- **Tên bảng vật lý**: `fea_data_schema_table_7`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_7_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_7_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.8. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_8
- **Tên bảng vật lý**: `fea_data_schema_table_8`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_8_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_8_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.9. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_9
- **Tên bảng vật lý**: `fea_data_schema_table_9`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_9_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_9_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.10. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_10
- **Tên bảng vật lý**: `fea_data_schema_table_10`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_10_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_10_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.11. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_11
- **Tên bảng vật lý**: `fea_data_schema_table_11`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_11_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_11_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.12. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_12
- **Tên bảng vật lý**: `fea_data_schema_table_12`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_12_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_12_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.13. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_13
- **Tên bảng vật lý**: `fea_data_schema_table_13`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_13_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_13_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.14. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_14
- **Tên bảng vật lý**: `fea_data_schema_table_14`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_14_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_14_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.15. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_15
- **Tên bảng vật lý**: `fea_data_schema_table_15`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_15_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_15_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.16. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_16
- **Tên bảng vật lý**: `fea_data_schema_table_16`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_16_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_16_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.17. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_17
- **Tên bảng vật lý**: `fea_data_schema_table_17`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_17_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_17_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.18. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_18
- **Tên bảng vật lý**: `fea_data_schema_table_18`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_18_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_18_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.19. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_19
- **Tên bảng vật lý**: `fea_data_schema_table_19`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_19_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_19_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.20. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_20
- **Tên bảng vật lý**: `fea_data_schema_table_20`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_20_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_20_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.21. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_21
- **Tên bảng vật lý**: `fea_data_schema_table_21`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_21_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_21_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.22. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_22
- **Tên bảng vật lý**: `fea_data_schema_table_22`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_22_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_22_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.23. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_23
- **Tên bảng vật lý**: `fea_data_schema_table_23`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_23_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_23_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.24. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_24
- **Tên bảng vật lý**: `fea_data_schema_table_24`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_24_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_24_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.25. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_25
- **Tên bảng vật lý**: `fea_data_schema_table_25`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_25_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_25_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.26. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_26
- **Tên bảng vật lý**: `fea_data_schema_table_26`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_26_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_26_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.27. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_27
- **Tên bảng vật lý**: `fea_data_schema_table_27`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_27_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_27_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.28. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_28
- **Tên bảng vật lý**: `fea_data_schema_table_28`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_28_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_28_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.29. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_29
- **Tên bảng vật lý**: `fea_data_schema_table_29`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_29_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_29_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.30. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_30
- **Tên bảng vật lý**: `fea_data_schema_table_30`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_30_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_30_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.31. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_31
- **Tên bảng vật lý**: `fea_data_schema_table_31`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_31_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_31_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.32. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_32
- **Tên bảng vật lý**: `fea_data_schema_table_32`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_32_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_32_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.33. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_33
- **Tên bảng vật lý**: `fea_data_schema_table_33`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_33_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_33_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.34. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_34
- **Tên bảng vật lý**: `fea_data_schema_table_34`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_34_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_34_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.35. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_35
- **Tên bảng vật lý**: `fea_data_schema_table_35`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_35_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_35_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.36. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_36
- **Tên bảng vật lý**: `fea_data_schema_table_36`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_36_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_36_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.37. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_37
- **Tên bảng vật lý**: `fea_data_schema_table_37`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_37_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_37_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.38. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_38
- **Tên bảng vật lý**: `fea_data_schema_table_38`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_38_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_38_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.39. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_39
- **Tên bảng vật lý**: `fea_data_schema_table_39`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_39_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_39_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.40. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_40
- **Tên bảng vật lý**: `fea_data_schema_table_40`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_40_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_40_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.41. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_41
- **Tên bảng vật lý**: `fea_data_schema_table_41`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_41_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_41_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.42. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_42
- **Tên bảng vật lý**: `fea_data_schema_table_42`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_42_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_42_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.43. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_43
- **Tên bảng vật lý**: `fea_data_schema_table_43`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_43_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_43_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.44. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_44
- **Tên bảng vật lý**: `fea_data_schema_table_44`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_44_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_44_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.45. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_45
- **Tên bảng vật lý**: `fea_data_schema_table_45`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_45_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_45_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.46. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_46
- **Tên bảng vật lý**: `fea_data_schema_table_46`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_46_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_46_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.47. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_47
- **Tên bảng vật lý**: `fea_data_schema_table_47`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_47_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_47_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.48. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_48
- **Tên bảng vật lý**: `fea_data_schema_table_48`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_48_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_48_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.49. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_49
- **Tên bảng vật lý**: `fea_data_schema_table_49`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_49_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_49_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.50. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_50
- **Tên bảng vật lý**: `fea_data_schema_table_50`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_50_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_50_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.51. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_51
- **Tên bảng vật lý**: `fea_data_schema_table_51`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_51_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_51_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.52. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_52
- **Tên bảng vật lý**: `fea_data_schema_table_52`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_52_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_52_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.53. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_53
- **Tên bảng vật lý**: `fea_data_schema_table_53`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_53_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_53_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.54. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_54
- **Tên bảng vật lý**: `fea_data_schema_table_54`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_54_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_54_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.55. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_55
- **Tên bảng vật lý**: `fea_data_schema_table_55`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_55_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_55_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.56. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_56
- **Tên bảng vật lý**: `fea_data_schema_table_56`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_56_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_56_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.57. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_57
- **Tên bảng vật lý**: `fea_data_schema_table_57`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_57_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_57_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.58. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_58
- **Tên bảng vật lý**: `fea_data_schema_table_58`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_58_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_58_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.59. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_59
- **Tên bảng vật lý**: `fea_data_schema_table_59`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_59_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_59_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.60. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_60
- **Tên bảng vật lý**: `fea_data_schema_table_60`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_60_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_60_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.61. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_61
- **Tên bảng vật lý**: `fea_data_schema_table_61`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_61_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_61_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.62. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_62
- **Tên bảng vật lý**: `fea_data_schema_table_62`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_62_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_62_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.63. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_63
- **Tên bảng vật lý**: `fea_data_schema_table_63`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_63_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_63_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.64. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_64
- **Tên bảng vật lý**: `fea_data_schema_table_64`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_64_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_64_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.65. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_65
- **Tên bảng vật lý**: `fea_data_schema_table_65`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_65_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_65_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.66. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_66
- **Tên bảng vật lý**: `fea_data_schema_table_66`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_66_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_66_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.67. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_67
- **Tên bảng vật lý**: `fea_data_schema_table_67`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_67_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_67_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.68. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_68
- **Tên bảng vật lý**: `fea_data_schema_table_68`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_68_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_68_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.69. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_69
- **Tên bảng vật lý**: `fea_data_schema_table_69`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_69_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_69_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.70. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_70
- **Tên bảng vật lý**: `fea_data_schema_table_70`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_70_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_70_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.71. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_71
- **Tên bảng vật lý**: `fea_data_schema_table_71`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_71_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_71_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.72. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_72
- **Tên bảng vật lý**: `fea_data_schema_table_72`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_72_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_72_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.73. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_73
- **Tên bảng vật lý**: `fea_data_schema_table_73`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_73_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_73_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.74. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_74
- **Tên bảng vật lý**: `fea_data_schema_table_74`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_74_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_74_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.75. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_75
- **Tên bảng vật lý**: `fea_data_schema_table_75`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_75_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_75_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.76. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_76
- **Tên bảng vật lý**: `fea_data_schema_table_76`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_76_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_76_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.77. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_77
- **Tên bảng vật lý**: `fea_data_schema_table_77`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_77_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_77_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.78. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_78
- **Tên bảng vật lý**: `fea_data_schema_table_78`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_78_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_78_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.79. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_79
- **Tên bảng vật lý**: `fea_data_schema_table_79`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_79_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_79_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.80. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_80
- **Tên bảng vật lý**: `fea_data_schema_table_80`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_80_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_80_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.81. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_81
- **Tên bảng vật lý**: `fea_data_schema_table_81`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_81_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_81_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.82. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_82
- **Tên bảng vật lý**: `fea_data_schema_table_82`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_82_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_82_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.83. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_83
- **Tên bảng vật lý**: `fea_data_schema_table_83`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_83_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_83_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.84. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_84
- **Tên bảng vật lý**: `fea_data_schema_table_84`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_84_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_84_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.85. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_85
- **Tên bảng vật lý**: `fea_data_schema_table_85`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_85_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_85_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.86. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_86
- **Tên bảng vật lý**: `fea_data_schema_table_86`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_86_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_86_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.87. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_87
- **Tên bảng vật lý**: `fea_data_schema_table_87`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_87_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_87_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.88. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_88
- **Tên bảng vật lý**: `fea_data_schema_table_88`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_88_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_88_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.89. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_89
- **Tên bảng vật lý**: `fea_data_schema_table_89`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_89_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_89_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.90. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_90
- **Tên bảng vật lý**: `fea_data_schema_table_90`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_90_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_90_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.91. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_91
- **Tên bảng vật lý**: `fea_data_schema_table_91`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_91_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_91_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.92. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_92
- **Tên bảng vật lý**: `fea_data_schema_table_92`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_92_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_92_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.93. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_93
- **Tên bảng vật lý**: `fea_data_schema_table_93`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_93_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_93_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.94. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_94
- **Tên bảng vật lý**: `fea_data_schema_table_94`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_94_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_94_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.95. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_95
- **Tên bảng vật lý**: `fea_data_schema_table_95`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_95_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_95_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.96. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_96
- **Tên bảng vật lý**: `fea_data_schema_table_96`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_96_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_96_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.97. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_97
- **Tên bảng vật lý**: `fea_data_schema_table_97`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_97_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_97_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.98. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_98
- **Tên bảng vật lý**: `fea_data_schema_table_98`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_98_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_98_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.99. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_99
- **Tên bảng vật lý**: `fea_data_schema_table_99`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_99_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_99_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.100. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_100
- **Tên bảng vật lý**: `fea_data_schema_table_100`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_100_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_100_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.101. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_101
- **Tên bảng vật lý**: `fea_data_schema_table_101`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_101_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_101_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.102. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_102
- **Tên bảng vật lý**: `fea_data_schema_table_102`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_102_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_102_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.103. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_103
- **Tên bảng vật lý**: `fea_data_schema_table_103`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_103_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_103_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.104. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_104
- **Tên bảng vật lý**: `fea_data_schema_table_104`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_104_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_104_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.105. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_105
- **Tên bảng vật lý**: `fea_data_schema_table_105`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_105_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_105_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.106. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_106
- **Tên bảng vật lý**: `fea_data_schema_table_106`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_106_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_106_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.107. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_107
- **Tên bảng vật lý**: `fea_data_schema_table_107`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_107_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_107_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.108. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_108
- **Tên bảng vật lý**: `fea_data_schema_table_108`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_108_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_108_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.109. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_109
- **Tên bảng vật lý**: `fea_data_schema_table_109`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_109_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_109_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.110. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_110
- **Tên bảng vật lý**: `fea_data_schema_table_110`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_110_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_110_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.111. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_111
- **Tên bảng vật lý**: `fea_data_schema_table_111`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_111_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_111_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.112. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_112
- **Tên bảng vật lý**: `fea_data_schema_table_112`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_112_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_112_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.113. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_113
- **Tên bảng vật lý**: `fea_data_schema_table_113`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_113_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_113_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.114. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_114
- **Tên bảng vật lý**: `fea_data_schema_table_114`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_114_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_114_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.115. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_115
- **Tên bảng vật lý**: `fea_data_schema_table_115`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_115_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_115_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.116. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_116
- **Tên bảng vật lý**: `fea_data_schema_table_116`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_116_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_116_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.117. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_117
- **Tên bảng vật lý**: `fea_data_schema_table_117`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_117_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_117_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.118. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_118
- **Tên bảng vật lý**: `fea_data_schema_table_118`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_118_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_118_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.119. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_119
- **Tên bảng vật lý**: `fea_data_schema_table_119`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_119_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_119_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.120. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_120
- **Tên bảng vật lý**: `fea_data_schema_table_120`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_120_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_120_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.121. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_121
- **Tên bảng vật lý**: `fea_data_schema_table_121`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_121_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_121_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.122. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_122
- **Tên bảng vật lý**: `fea_data_schema_table_122`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_122_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_122_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.123. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_123
- **Tên bảng vật lý**: `fea_data_schema_table_123`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_123_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_123_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.124. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_124
- **Tên bảng vật lý**: `fea_data_schema_table_124`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_124_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_124_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.125. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_125
- **Tên bảng vật lý**: `fea_data_schema_table_125`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_125_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_125_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.126. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_126
- **Tên bảng vật lý**: `fea_data_schema_table_126`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_126_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_126_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.127. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_127
- **Tên bảng vật lý**: `fea_data_schema_table_127`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_127_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_127_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.128. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_128
- **Tên bảng vật lý**: `fea_data_schema_table_128`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_128_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_128_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.129. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_129
- **Tên bảng vật lý**: `fea_data_schema_table_129`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_129_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_129_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.130. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_130
- **Tên bảng vật lý**: `fea_data_schema_table_130`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_130_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_130_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.131. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_131
- **Tên bảng vật lý**: `fea_data_schema_table_131`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_131_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_131_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.132. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_132
- **Tên bảng vật lý**: `fea_data_schema_table_132`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_132_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_132_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.133. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_133
- **Tên bảng vật lý**: `fea_data_schema_table_133`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_133_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_133_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.134. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_134
- **Tên bảng vật lý**: `fea_data_schema_table_134`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_134_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_134_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.135. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_135
- **Tên bảng vật lý**: `fea_data_schema_table_135`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_135_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_135_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.136. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_136
- **Tên bảng vật lý**: `fea_data_schema_table_136`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_136_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_136_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.137. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_137
- **Tên bảng vật lý**: `fea_data_schema_table_137`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_137_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_137_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.138. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_138
- **Tên bảng vật lý**: `fea_data_schema_table_138`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_138_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_138_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.139. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_139
- **Tên bảng vật lý**: `fea_data_schema_table_139`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_139_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_139_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.140. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_140
- **Tên bảng vật lý**: `fea_data_schema_table_140`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_140_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_140_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.141. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_141
- **Tên bảng vật lý**: `fea_data_schema_table_141`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_141_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_141_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.142. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_142
- **Tên bảng vật lý**: `fea_data_schema_table_142`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_142_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_142_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.143. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_143
- **Tên bảng vật lý**: `fea_data_schema_table_143`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_143_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_143_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.144. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_144
- **Tên bảng vật lý**: `fea_data_schema_table_144`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_144_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_144_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.145. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_145
- **Tên bảng vật lý**: `fea_data_schema_table_145`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_145_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_145_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.146. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_146
- **Tên bảng vật lý**: `fea_data_schema_table_146`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_146_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_146_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.147. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_147
- **Tên bảng vật lý**: `fea_data_schema_table_147`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_147_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_147_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.148. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_148
- **Tên bảng vật lý**: `fea_data_schema_table_148`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_148_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_148_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.149. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_149
- **Tên bảng vật lý**: `fea_data_schema_table_149`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_149_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_149_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.150. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_150
- **Tên bảng vật lý**: `fea_data_schema_table_150`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_150_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_150_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.151. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_151
- **Tên bảng vật lý**: `fea_data_schema_table_151`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_151_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_151_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.152. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_152
- **Tên bảng vật lý**: `fea_data_schema_table_152`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_152_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_152_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.153. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_153
- **Tên bảng vật lý**: `fea_data_schema_table_153`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_153_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_153_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.154. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_154
- **Tên bảng vật lý**: `fea_data_schema_table_154`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_154_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_154_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.155. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_155
- **Tên bảng vật lý**: `fea_data_schema_table_155`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_155_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_155_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.156. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_156
- **Tên bảng vật lý**: `fea_data_schema_table_156`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_156_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_156_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.157. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_157
- **Tên bảng vật lý**: `fea_data_schema_table_157`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_157_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_157_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.158. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_158
- **Tên bảng vật lý**: `fea_data_schema_table_158`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_158_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_158_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.159. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_159
- **Tên bảng vật lý**: `fea_data_schema_table_159`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_159_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_159_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.160. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_160
- **Tên bảng vật lý**: `fea_data_schema_table_160`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_160_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_160_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.161. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_161
- **Tên bảng vật lý**: `fea_data_schema_table_161`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_161_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_161_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.162. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_162
- **Tên bảng vật lý**: `fea_data_schema_table_162`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_162_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_162_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.163. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_163
- **Tên bảng vật lý**: `fea_data_schema_table_163`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_163_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_163_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.164. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_164
- **Tên bảng vật lý**: `fea_data_schema_table_164`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_164_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_164_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.165. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_165
- **Tên bảng vật lý**: `fea_data_schema_table_165`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_165_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_165_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.166. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_166
- **Tên bảng vật lý**: `fea_data_schema_table_166`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_166_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_166_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.167. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_167
- **Tên bảng vật lý**: `fea_data_schema_table_167`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_167_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_167_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.168. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_168
- **Tên bảng vật lý**: `fea_data_schema_table_168`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_168_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_168_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.169. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_169
- **Tên bảng vật lý**: `fea_data_schema_table_169`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_169_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_169_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.170. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_170
- **Tên bảng vật lý**: `fea_data_schema_table_170`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_170_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_170_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.171. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_171
- **Tên bảng vật lý**: `fea_data_schema_table_171`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_171_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_171_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.172. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_172
- **Tên bảng vật lý**: `fea_data_schema_table_172`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_172_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_172_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.173. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_173
- **Tên bảng vật lý**: `fea_data_schema_table_173`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_173_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_173_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.174. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_174
- **Tên bảng vật lý**: `fea_data_schema_table_174`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_174_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_174_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.175. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_175
- **Tên bảng vật lý**: `fea_data_schema_table_175`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_175_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_175_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.176. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_176
- **Tên bảng vật lý**: `fea_data_schema_table_176`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_176_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_176_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.177. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_177
- **Tên bảng vật lý**: `fea_data_schema_table_177`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_177_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_177_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.178. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_178
- **Tên bảng vật lý**: `fea_data_schema_table_178`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_178_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_178_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.179. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_179
- **Tên bảng vật lý**: `fea_data_schema_table_179`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_179_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_179_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.180. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_180
- **Tên bảng vật lý**: `fea_data_schema_table_180`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_180_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_180_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.181. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_181
- **Tên bảng vật lý**: `fea_data_schema_table_181`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_181_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_181_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.182. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_182
- **Tên bảng vật lý**: `fea_data_schema_table_182`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_182_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_182_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.183. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_183
- **Tên bảng vật lý**: `fea_data_schema_table_183`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_183_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_183_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.184. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_184
- **Tên bảng vật lý**: `fea_data_schema_table_184`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_184_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_184_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.185. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_185
- **Tên bảng vật lý**: `fea_data_schema_table_185`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_185_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_185_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.186. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_186
- **Tên bảng vật lý**: `fea_data_schema_table_186`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_186_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_186_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.187. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_187
- **Tên bảng vật lý**: `fea_data_schema_table_187`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_187_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_187_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.188. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_188
- **Tên bảng vật lý**: `fea_data_schema_table_188`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_188_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_188_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.189. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_189
- **Tên bảng vật lý**: `fea_data_schema_table_189`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_189_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_189_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.190. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_190
- **Tên bảng vật lý**: `fea_data_schema_table_190`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_190_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_190_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.191. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_191
- **Tên bảng vật lý**: `fea_data_schema_table_191`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_191_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_191_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.192. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_192
- **Tên bảng vật lý**: `fea_data_schema_table_192`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_192_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_192_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.193. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_193
- **Tên bảng vật lý**: `fea_data_schema_table_193`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_193_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_193_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.194. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_194
- **Tên bảng vật lý**: `fea_data_schema_table_194`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_194_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_194_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.195. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_195
- **Tên bảng vật lý**: `fea_data_schema_table_195`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_195_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_195_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.196. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_196
- **Tên bảng vật lý**: `fea_data_schema_table_196`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_196_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_196_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.197. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_197
- **Tên bảng vật lý**: `fea_data_schema_table_197`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_197_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_197_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.198. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_198
- **Tên bảng vật lý**: `fea_data_schema_table_198`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_198_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_198_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.199. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_199
- **Tên bảng vật lý**: `fea_data_schema_table_199`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_199_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_199_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.200. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_200
- **Tên bảng vật lý**: `fea_data_schema_table_200`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_200_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_200_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.201. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_201
- **Tên bảng vật lý**: `fea_data_schema_table_201`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_201_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_201_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.202. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_202
- **Tên bảng vật lý**: `fea_data_schema_table_202`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_202_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_202_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.203. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_203
- **Tên bảng vật lý**: `fea_data_schema_table_203`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_203_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_203_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.204. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_204
- **Tên bảng vật lý**: `fea_data_schema_table_204`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_204_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_204_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.205. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_205
- **Tên bảng vật lý**: `fea_data_schema_table_205`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_205_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_205_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.206. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_206
- **Tên bảng vật lý**: `fea_data_schema_table_206`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_206_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_206_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.207. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_207
- **Tên bảng vật lý**: `fea_data_schema_table_207`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_207_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_207_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.208. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_208
- **Tên bảng vật lý**: `fea_data_schema_table_208`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_208_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_208_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.209. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_209
- **Tên bảng vật lý**: `fea_data_schema_table_209`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_209_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_209_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.210. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_210
- **Tên bảng vật lý**: `fea_data_schema_table_210`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_210_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_210_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.211. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_211
- **Tên bảng vật lý**: `fea_data_schema_table_211`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_211_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_211_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.212. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_212
- **Tên bảng vật lý**: `fea_data_schema_table_212`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_212_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_212_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.213. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_213
- **Tên bảng vật lý**: `fea_data_schema_table_213`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_213_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_213_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.214. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_214
- **Tên bảng vật lý**: `fea_data_schema_table_214`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_214_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_214_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.215. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_215
- **Tên bảng vật lý**: `fea_data_schema_table_215`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_215_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_215_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.216. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_216
- **Tên bảng vật lý**: `fea_data_schema_table_216`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_216_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_216_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.217. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_217
- **Tên bảng vật lý**: `fea_data_schema_table_217`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_217_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_217_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.218. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_218
- **Tên bảng vật lý**: `fea_data_schema_table_218`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_218_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_218_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.219. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_219
- **Tên bảng vật lý**: `fea_data_schema_table_219`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_219_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_219_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.220. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_220
- **Tên bảng vật lý**: `fea_data_schema_table_220`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_220_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_220_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.221. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_221
- **Tên bảng vật lý**: `fea_data_schema_table_221`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_221_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_221_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.222. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_222
- **Tên bảng vật lý**: `fea_data_schema_table_222`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_222_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_222_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.223. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_223
- **Tên bảng vật lý**: `fea_data_schema_table_223`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_223_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_223_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.224. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_224
- **Tên bảng vật lý**: `fea_data_schema_table_224`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_224_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_224_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.225. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_225
- **Tên bảng vật lý**: `fea_data_schema_table_225`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_225_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_225_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.226. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_226
- **Tên bảng vật lý**: `fea_data_schema_table_226`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_226_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_226_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.227. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_227
- **Tên bảng vật lý**: `fea_data_schema_table_227`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_227_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_227_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.228. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_228
- **Tên bảng vật lý**: `fea_data_schema_table_228`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_228_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_228_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.229. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_229
- **Tên bảng vật lý**: `fea_data_schema_table_229`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_229_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_229_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.230. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_230
- **Tên bảng vật lý**: `fea_data_schema_table_230`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_230_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_230_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.231. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_231
- **Tên bảng vật lý**: `fea_data_schema_table_231`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_231_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_231_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.232. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_232
- **Tên bảng vật lý**: `fea_data_schema_table_232`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_232_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_232_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.233. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_233
- **Tên bảng vật lý**: `fea_data_schema_table_233`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_233_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_233_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.234. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_234
- **Tên bảng vật lý**: `fea_data_schema_table_234`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_234_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_234_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.235. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_235
- **Tên bảng vật lý**: `fea_data_schema_table_235`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_235_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_235_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.236. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_236
- **Tên bảng vật lý**: `fea_data_schema_table_236`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_236_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_236_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.237. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_237
- **Tên bảng vật lý**: `fea_data_schema_table_237`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_237_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_237_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.238. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_238
- **Tên bảng vật lý**: `fea_data_schema_table_238`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_238_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_238_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.239. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_239
- **Tên bảng vật lý**: `fea_data_schema_table_239`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_239_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_239_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.240. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_240
- **Tên bảng vật lý**: `fea_data_schema_table_240`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_240_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_240_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.241. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_241
- **Tên bảng vật lý**: `fea_data_schema_table_241`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_241_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_241_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.242. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_242
- **Tên bảng vật lý**: `fea_data_schema_table_242`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_242_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_242_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.243. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_243
- **Tên bảng vật lý**: `fea_data_schema_table_243`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_243_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_243_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.244. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_244
- **Tên bảng vật lý**: `fea_data_schema_table_244`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_244_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_244_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.245. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_245
- **Tên bảng vật lý**: `fea_data_schema_table_245`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_245_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_245_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.246. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_246
- **Tên bảng vật lý**: `fea_data_schema_table_246`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_246_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_246_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.247. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_247
- **Tên bảng vật lý**: `fea_data_schema_table_247`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_247_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_247_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.248. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_248
- **Tên bảng vật lý**: `fea_data_schema_table_248`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_248_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_248_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.249. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_249
- **Tên bảng vật lý**: `fea_data_schema_table_249`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_249_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_249_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.250. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_250
- **Tên bảng vật lý**: `fea_data_schema_table_250`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_250_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_250_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.251. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_251
- **Tên bảng vật lý**: `fea_data_schema_table_251`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_251_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_251_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.252. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_252
- **Tên bảng vật lý**: `fea_data_schema_table_252`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_252_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_252_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.253. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_253
- **Tên bảng vật lý**: `fea_data_schema_table_253`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_253_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_253_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.254. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_254
- **Tên bảng vật lý**: `fea_data_schema_table_254`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_254_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_254_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.255. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_255
- **Tên bảng vật lý**: `fea_data_schema_table_255`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_255_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_255_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.256. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_256
- **Tên bảng vật lý**: `fea_data_schema_table_256`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_256_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_256_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.257. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_257
- **Tên bảng vật lý**: `fea_data_schema_table_257`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_257_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_257_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.258. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_258
- **Tên bảng vật lý**: `fea_data_schema_table_258`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_258_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_258_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.259. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_259
- **Tên bảng vật lý**: `fea_data_schema_table_259`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_259_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_259_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.260. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_260
- **Tên bảng vật lý**: `fea_data_schema_table_260`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_260_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_260_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.261. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_261
- **Tên bảng vật lý**: `fea_data_schema_table_261`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_261_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_261_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.262. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_262
- **Tên bảng vật lý**: `fea_data_schema_table_262`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_262_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_262_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.263. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_263
- **Tên bảng vật lý**: `fea_data_schema_table_263`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_263_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_263_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.264. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_264
- **Tên bảng vật lý**: `fea_data_schema_table_264`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_264_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_264_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.265. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_265
- **Tên bảng vật lý**: `fea_data_schema_table_265`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_265_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_265_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.266. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_266
- **Tên bảng vật lý**: `fea_data_schema_table_266`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_266_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_266_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.267. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_267
- **Tên bảng vật lý**: `fea_data_schema_table_267`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_267_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_267_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.268. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_268
- **Tên bảng vật lý**: `fea_data_schema_table_268`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_268_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_268_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.269. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_269
- **Tên bảng vật lý**: `fea_data_schema_table_269`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_269_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_269_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.270. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_270
- **Tên bảng vật lý**: `fea_data_schema_table_270`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_270_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_270_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.271. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_271
- **Tên bảng vật lý**: `fea_data_schema_table_271`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_271_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_271_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.272. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_272
- **Tên bảng vật lý**: `fea_data_schema_table_272`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_272_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_272_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.273. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_273
- **Tên bảng vật lý**: `fea_data_schema_table_273`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_273_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_273_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.274. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_274
- **Tên bảng vật lý**: `fea_data_schema_table_274`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_274_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_274_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.275. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_275
- **Tên bảng vật lý**: `fea_data_schema_table_275`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_275_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_275_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.276. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_276
- **Tên bảng vật lý**: `fea_data_schema_table_276`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_276_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_276_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.277. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_277
- **Tên bảng vật lý**: `fea_data_schema_table_277`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_277_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_277_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.278. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_278
- **Tên bảng vật lý**: `fea_data_schema_table_278`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_278_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_278_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.279. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_279
- **Tên bảng vật lý**: `fea_data_schema_table_279`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_279_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_279_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.280. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_280
- **Tên bảng vật lý**: `fea_data_schema_table_280`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_280_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_280_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.281. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_281
- **Tên bảng vật lý**: `fea_data_schema_table_281`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_281_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_281_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.282. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_282
- **Tên bảng vật lý**: `fea_data_schema_table_282`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_282_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_282_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.283. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_283
- **Tên bảng vật lý**: `fea_data_schema_table_283`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_283_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_283_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.284. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_284
- **Tên bảng vật lý**: `fea_data_schema_table_284`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_284_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_284_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.285. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_285
- **Tên bảng vật lý**: `fea_data_schema_table_285`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_285_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_285_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.286. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_286
- **Tên bảng vật lý**: `fea_data_schema_table_286`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_286_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_286_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.287. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_287
- **Tên bảng vật lý**: `fea_data_schema_table_287`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_287_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_287_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.288. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_288
- **Tên bảng vật lý**: `fea_data_schema_table_288`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_288_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_288_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.289. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_289
- **Tên bảng vật lý**: `fea_data_schema_table_289`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_289_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_289_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.290. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_290
- **Tên bảng vật lý**: `fea_data_schema_table_290`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_290_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_290_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.291. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_291
- **Tên bảng vật lý**: `fea_data_schema_table_291`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_291_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_291_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.292. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_292
- **Tên bảng vật lý**: `fea_data_schema_table_292`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_292_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_292_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.293. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_293
- **Tên bảng vật lý**: `fea_data_schema_table_293`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_293_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_293_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.294. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_294
- **Tên bảng vật lý**: `fea_data_schema_table_294`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_294_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_294_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.295. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_295
- **Tên bảng vật lý**: `fea_data_schema_table_295`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_295_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_295_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.296. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_296
- **Tên bảng vật lý**: `fea_data_schema_table_296`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_296_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_296_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.297. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_297
- **Tên bảng vật lý**: `fea_data_schema_table_297`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_297_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_297_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.298. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_298
- **Tên bảng vật lý**: `fea_data_schema_table_298`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_298_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_298_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.299. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_299
- **Tên bảng vật lý**: `fea_data_schema_table_299`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_299_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_299_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.300. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_300
- **Tên bảng vật lý**: `fea_data_schema_table_300`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_300_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_300_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.301. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_301
- **Tên bảng vật lý**: `fea_data_schema_table_301`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_301_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_301_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.302. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_302
- **Tên bảng vật lý**: `fea_data_schema_table_302`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_302_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_302_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.303. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_303
- **Tên bảng vật lý**: `fea_data_schema_table_303`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_303_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_303_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.304. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_304
- **Tên bảng vật lý**: `fea_data_schema_table_304`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_304_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_304_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.305. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_305
- **Tên bảng vật lý**: `fea_data_schema_table_305`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_305_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_305_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.306. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_306
- **Tên bảng vật lý**: `fea_data_schema_table_306`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_306_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_306_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.307. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_307
- **Tên bảng vật lý**: `fea_data_schema_table_307`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_307_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_307_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.308. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_308
- **Tên bảng vật lý**: `fea_data_schema_table_308`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_308_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_308_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.309. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_309
- **Tên bảng vật lý**: `fea_data_schema_table_309`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_309_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_309_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.310. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_310
- **Tên bảng vật lý**: `fea_data_schema_table_310`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_310_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_310_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.311. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_311
- **Tên bảng vật lý**: `fea_data_schema_table_311`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_311_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_311_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.312. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_312
- **Tên bảng vật lý**: `fea_data_schema_table_312`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_312_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_312_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.313. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_313
- **Tên bảng vật lý**: `fea_data_schema_table_313`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_313_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_313_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.314. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_314
- **Tên bảng vật lý**: `fea_data_schema_table_314`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_314_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_314_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.315. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_315
- **Tên bảng vật lý**: `fea_data_schema_table_315`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_315_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_315_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.316. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_316
- **Tên bảng vật lý**: `fea_data_schema_table_316`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_316_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_316_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.317. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_317
- **Tên bảng vật lý**: `fea_data_schema_table_317`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_317_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_317_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.318. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_318
- **Tên bảng vật lý**: `fea_data_schema_table_318`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_318_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_318_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.319. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_319
- **Tên bảng vật lý**: `fea_data_schema_table_319`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_319_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_319_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.320. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_320
- **Tên bảng vật lý**: `fea_data_schema_table_320`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_320_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_320_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.321. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_321
- **Tên bảng vật lý**: `fea_data_schema_table_321`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_321_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_321_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.322. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_322
- **Tên bảng vật lý**: `fea_data_schema_table_322`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_322_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_322_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.323. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_323
- **Tên bảng vật lý**: `fea_data_schema_table_323`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_323_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_323_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.324. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_324
- **Tên bảng vật lý**: `fea_data_schema_table_324`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_324_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_324_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.325. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_325
- **Tên bảng vật lý**: `fea_data_schema_table_325`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_325_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_325_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.326. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_326
- **Tên bảng vật lý**: `fea_data_schema_table_326`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_326_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_326_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.327. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_327
- **Tên bảng vật lý**: `fea_data_schema_table_327`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_327_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_327_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.328. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_328
- **Tên bảng vật lý**: `fea_data_schema_table_328`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_328_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_328_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.329. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_329
- **Tên bảng vật lý**: `fea_data_schema_table_329`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_329_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_329_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.330. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_330
- **Tên bảng vật lý**: `fea_data_schema_table_330`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_330_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_330_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.331. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_331
- **Tên bảng vật lý**: `fea_data_schema_table_331`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_331_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_331_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.332. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_332
- **Tên bảng vật lý**: `fea_data_schema_table_332`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_332_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_332_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.333. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_333
- **Tên bảng vật lý**: `fea_data_schema_table_333`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_333_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_333_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.334. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_334
- **Tên bảng vật lý**: `fea_data_schema_table_334`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_334_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_334_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.335. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_335
- **Tên bảng vật lý**: `fea_data_schema_table_335`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_335_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_335_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.336. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_336
- **Tên bảng vật lý**: `fea_data_schema_table_336`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_336_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_336_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.337. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_337
- **Tên bảng vật lý**: `fea_data_schema_table_337`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_337_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_337_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.338. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_338
- **Tên bảng vật lý**: `fea_data_schema_table_338`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_338_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_338_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.339. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_339
- **Tên bảng vật lý**: `fea_data_schema_table_339`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_339_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_339_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.340. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_340
- **Tên bảng vật lý**: `fea_data_schema_table_340`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_340_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_340_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.341. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_341
- **Tên bảng vật lý**: `fea_data_schema_table_341`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_341_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_341_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.342. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_342
- **Tên bảng vật lý**: `fea_data_schema_table_342`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_342_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_342_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.343. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_343
- **Tên bảng vật lý**: `fea_data_schema_table_343`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_343_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_343_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.344. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_344
- **Tên bảng vật lý**: `fea_data_schema_table_344`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_344_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_344_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.345. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_345
- **Tên bảng vật lý**: `fea_data_schema_table_345`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_345_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_345_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.346. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_346
- **Tên bảng vật lý**: `fea_data_schema_table_346`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_346_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_346_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.347. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_347
- **Tên bảng vật lý**: `fea_data_schema_table_347`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_347_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_347_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.348. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_348
- **Tên bảng vật lý**: `fea_data_schema_table_348`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_348_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_348_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.349. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_349
- **Tên bảng vật lý**: `fea_data_schema_table_349`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_349_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_349_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.350. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_350
- **Tên bảng vật lý**: `fea_data_schema_table_350`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_350_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_350_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.351. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_351
- **Tên bảng vật lý**: `fea_data_schema_table_351`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_351_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_351_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.352. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_352
- **Tên bảng vật lý**: `fea_data_schema_table_352`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_352_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_352_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.353. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_353
- **Tên bảng vật lý**: `fea_data_schema_table_353`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_353_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_353_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.354. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_354
- **Tên bảng vật lý**: `fea_data_schema_table_354`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_354_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_354_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.355. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_355
- **Tên bảng vật lý**: `fea_data_schema_table_355`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_355_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_355_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.356. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_356
- **Tên bảng vật lý**: `fea_data_schema_table_356`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_356_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_356_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.357. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_357
- **Tên bảng vật lý**: `fea_data_schema_table_357`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_357_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_357_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.358. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_358
- **Tên bảng vật lý**: `fea_data_schema_table_358`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_358_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_358_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.359. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_359
- **Tên bảng vật lý**: `fea_data_schema_table_359`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_359_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_359_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.360. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_360
- **Tên bảng vật lý**: `fea_data_schema_table_360`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_360_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_360_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.361. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_361
- **Tên bảng vật lý**: `fea_data_schema_table_361`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_361_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_361_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.362. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_362
- **Tên bảng vật lý**: `fea_data_schema_table_362`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_362_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_362_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.363. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_363
- **Tên bảng vật lý**: `fea_data_schema_table_363`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_363_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_363_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.364. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_364
- **Tên bảng vật lý**: `fea_data_schema_table_364`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_364_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_364_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.365. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_365
- **Tên bảng vật lý**: `fea_data_schema_table_365`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_365_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_365_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.366. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_366
- **Tên bảng vật lý**: `fea_data_schema_table_366`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_366_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_366_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.367. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_367
- **Tên bảng vật lý**: `fea_data_schema_table_367`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_367_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_367_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.368. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_368
- **Tên bảng vật lý**: `fea_data_schema_table_368`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_368_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_368_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.369. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_369
- **Tên bảng vật lý**: `fea_data_schema_table_369`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_369_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_369_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.370. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_370
- **Tên bảng vật lý**: `fea_data_schema_table_370`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_370_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_370_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.371. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_371
- **Tên bảng vật lý**: `fea_data_schema_table_371`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_371_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_371_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.372. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_372
- **Tên bảng vật lý**: `fea_data_schema_table_372`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_372_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_372_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.373. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_373
- **Tên bảng vật lý**: `fea_data_schema_table_373`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_373_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_373_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.374. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_374
- **Tên bảng vật lý**: `fea_data_schema_table_374`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_374_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_374_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.375. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_375
- **Tên bảng vật lý**: `fea_data_schema_table_375`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_375_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_375_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.376. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_376
- **Tên bảng vật lý**: `fea_data_schema_table_376`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_376_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_376_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.377. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_377
- **Tên bảng vật lý**: `fea_data_schema_table_377`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_377_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_377_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.378. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_378
- **Tên bảng vật lý**: `fea_data_schema_table_378`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_378_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_378_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.379. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_379
- **Tên bảng vật lý**: `fea_data_schema_table_379`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_379_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_379_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.380. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_380
- **Tên bảng vật lý**: `fea_data_schema_table_380`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_380_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_380_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.381. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_381
- **Tên bảng vật lý**: `fea_data_schema_table_381`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_381_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_381_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.382. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_382
- **Tên bảng vật lý**: `fea_data_schema_table_382`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_382_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_382_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.383. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_383
- **Tên bảng vật lý**: `fea_data_schema_table_383`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_383_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_383_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.384. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_384
- **Tên bảng vật lý**: `fea_data_schema_table_384`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_384_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_384_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.385. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_385
- **Tên bảng vật lý**: `fea_data_schema_table_385`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_385_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_385_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.386. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_386
- **Tên bảng vật lý**: `fea_data_schema_table_386`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_386_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_386_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.387. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_387
- **Tên bảng vật lý**: `fea_data_schema_table_387`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_387_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_387_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.388. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_388
- **Tên bảng vật lý**: `fea_data_schema_table_388`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_388_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_388_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.389. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_389
- **Tên bảng vật lý**: `fea_data_schema_table_389`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_389_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_389_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.390. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_390
- **Tên bảng vật lý**: `fea_data_schema_table_390`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_390_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_390_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.391. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_391
- **Tên bảng vật lý**: `fea_data_schema_table_391`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_391_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_391_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.392. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_392
- **Tên bảng vật lý**: `fea_data_schema_table_392`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_392_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_392_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.393. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_393
- **Tên bảng vật lý**: `fea_data_schema_table_393`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_393_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_393_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.394. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_394
- **Tên bảng vật lý**: `fea_data_schema_table_394`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_394_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_394_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.395. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_395
- **Tên bảng vật lý**: `fea_data_schema_table_395`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_395_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_395_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.396. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_396
- **Tên bảng vật lý**: `fea_data_schema_table_396`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_396_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_396_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.397. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_397
- **Tên bảng vật lý**: `fea_data_schema_table_397`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_397_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_397_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.398. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_398
- **Tên bảng vật lý**: `fea_data_schema_table_398`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_398_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_398_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.399. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_399
- **Tên bảng vật lý**: `fea_data_schema_table_399`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_399_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_399_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.400. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_400
- **Tên bảng vật lý**: `fea_data_schema_table_400`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_400_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_400_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.401. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_401
- **Tên bảng vật lý**: `fea_data_schema_table_401`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_401_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_401_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.402. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_402
- **Tên bảng vật lý**: `fea_data_schema_table_402`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_402_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_402_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.403. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_403
- **Tên bảng vật lý**: `fea_data_schema_table_403`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_403_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_403_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.404. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_404
- **Tên bảng vật lý**: `fea_data_schema_table_404`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_404_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_404_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.405. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_405
- **Tên bảng vật lý**: `fea_data_schema_table_405`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_405_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_405_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.406. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_406
- **Tên bảng vật lý**: `fea_data_schema_table_406`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_406_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_406_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.407. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_407
- **Tên bảng vật lý**: `fea_data_schema_table_407`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_407_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_407_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.408. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_408
- **Tên bảng vật lý**: `fea_data_schema_table_408`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_408_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_408_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.409. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_409
- **Tên bảng vật lý**: `fea_data_schema_table_409`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_409_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_409_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.410. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_410
- **Tên bảng vật lý**: `fea_data_schema_table_410`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_410_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_410_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.411. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_411
- **Tên bảng vật lý**: `fea_data_schema_table_411`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_411_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_411_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.412. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_412
- **Tên bảng vật lý**: `fea_data_schema_table_412`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_412_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_412_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.413. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_413
- **Tên bảng vật lý**: `fea_data_schema_table_413`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_413_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_413_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.414. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_414
- **Tên bảng vật lý**: `fea_data_schema_table_414`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_414_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_414_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.415. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_415
- **Tên bảng vật lý**: `fea_data_schema_table_415`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_415_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_415_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.416. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_416
- **Tên bảng vật lý**: `fea_data_schema_table_416`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_416_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_416_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.417. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_417
- **Tên bảng vật lý**: `fea_data_schema_table_417`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_417_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_417_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.418. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_418
- **Tên bảng vật lý**: `fea_data_schema_table_418`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_418_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_418_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.419. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_419
- **Tên bảng vật lý**: `fea_data_schema_table_419`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_419_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_419_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.420. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_420
- **Tên bảng vật lý**: `fea_data_schema_table_420`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_420_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_420_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.421. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_421
- **Tên bảng vật lý**: `fea_data_schema_table_421`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_421_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_421_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.422. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_422
- **Tên bảng vật lý**: `fea_data_schema_table_422`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_422_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_422_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.423. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_423
- **Tên bảng vật lý**: `fea_data_schema_table_423`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_423_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_423_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.424. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_424
- **Tên bảng vật lý**: `fea_data_schema_table_424`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_424_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_424_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.425. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_425
- **Tên bảng vật lý**: `fea_data_schema_table_425`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_425_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_425_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.426. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_426
- **Tên bảng vật lý**: `fea_data_schema_table_426`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_426_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_426_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.427. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_427
- **Tên bảng vật lý**: `fea_data_schema_table_427`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_427_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_427_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.428. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_428
- **Tên bảng vật lý**: `fea_data_schema_table_428`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_428_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_428_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.429. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_429
- **Tên bảng vật lý**: `fea_data_schema_table_429`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_429_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_429_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.430. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_430
- **Tên bảng vật lý**: `fea_data_schema_table_430`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_430_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_430_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.431. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_431
- **Tên bảng vật lý**: `fea_data_schema_table_431`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_431_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_431_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.432. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_432
- **Tên bảng vật lý**: `fea_data_schema_table_432`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_432_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_432_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.433. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_433
- **Tên bảng vật lý**: `fea_data_schema_table_433`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_433_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_433_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.434. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_434
- **Tên bảng vật lý**: `fea_data_schema_table_434`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_434_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_434_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.435. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_435
- **Tên bảng vật lý**: `fea_data_schema_table_435`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_435_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_435_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.436. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_436
- **Tên bảng vật lý**: `fea_data_schema_table_436`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_436_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_436_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.437. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_437
- **Tên bảng vật lý**: `fea_data_schema_table_437`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_437_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_437_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.438. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_438
- **Tên bảng vật lý**: `fea_data_schema_table_438`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_438_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_438_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.439. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_439
- **Tên bảng vật lý**: `fea_data_schema_table_439`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_439_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_439_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.440. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_440
- **Tên bảng vật lý**: `fea_data_schema_table_440`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_440_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_440_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.441. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_441
- **Tên bảng vật lý**: `fea_data_schema_table_441`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_441_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_441_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.442. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_442
- **Tên bảng vật lý**: `fea_data_schema_table_442`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_442_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_442_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.443. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_443
- **Tên bảng vật lý**: `fea_data_schema_table_443`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_443_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_443_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.444. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_444
- **Tên bảng vật lý**: `fea_data_schema_table_444`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_444_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_444_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.445. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_445
- **Tên bảng vật lý**: `fea_data_schema_table_445`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_445_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_445_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.446. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_446
- **Tên bảng vật lý**: `fea_data_schema_table_446`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_446_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_446_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.447. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_447
- **Tên bảng vật lý**: `fea_data_schema_table_447`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_447_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_447_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.448. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_448
- **Tên bảng vật lý**: `fea_data_schema_table_448`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_448_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_448_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.449. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_449
- **Tên bảng vật lý**: `fea_data_schema_table_449`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_449_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_449_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.450. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_450
- **Tên bảng vật lý**: `fea_data_schema_table_450`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_450_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_450_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.451. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_451
- **Tên bảng vật lý**: `fea_data_schema_table_451`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_451_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_451_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.452. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_452
- **Tên bảng vật lý**: `fea_data_schema_table_452`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_452_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_452_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.453. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_453
- **Tên bảng vật lý**: `fea_data_schema_table_453`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_453_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_453_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.454. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_454
- **Tên bảng vật lý**: `fea_data_schema_table_454`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_454_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_454_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.455. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_455
- **Tên bảng vật lý**: `fea_data_schema_table_455`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_455_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_455_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.456. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_456
- **Tên bảng vật lý**: `fea_data_schema_table_456`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_456_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_456_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.457. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_457
- **Tên bảng vật lý**: `fea_data_schema_table_457`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_457_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_457_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.458. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_458
- **Tên bảng vật lý**: `fea_data_schema_table_458`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_458_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_458_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.459. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_459
- **Tên bảng vật lý**: `fea_data_schema_table_459`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_459_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_459_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.460. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_460
- **Tên bảng vật lý**: `fea_data_schema_table_460`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_460_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_460_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.461. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_461
- **Tên bảng vật lý**: `fea_data_schema_table_461`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_461_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_461_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.462. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_462
- **Tên bảng vật lý**: `fea_data_schema_table_462`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_462_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_462_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.463. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_463
- **Tên bảng vật lý**: `fea_data_schema_table_463`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_463_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_463_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.464. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_464
- **Tên bảng vật lý**: `fea_data_schema_table_464`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_464_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_464_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.465. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_465
- **Tên bảng vật lý**: `fea_data_schema_table_465`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_465_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_465_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.466. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_466
- **Tên bảng vật lý**: `fea_data_schema_table_466`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_466_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_466_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.467. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_467
- **Tên bảng vật lý**: `fea_data_schema_table_467`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_467_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_467_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.468. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_468
- **Tên bảng vật lý**: `fea_data_schema_table_468`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_468_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_468_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.469. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_469
- **Tên bảng vật lý**: `fea_data_schema_table_469`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_469_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_469_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.470. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_470
- **Tên bảng vật lý**: `fea_data_schema_table_470`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_470_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_470_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.471. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_471
- **Tên bảng vật lý**: `fea_data_schema_table_471`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_471_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_471_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.472. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_472
- **Tên bảng vật lý**: `fea_data_schema_table_472`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_472_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_472_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.473. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_473
- **Tên bảng vật lý**: `fea_data_schema_table_473`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_473_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_473_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.474. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_474
- **Tên bảng vật lý**: `fea_data_schema_table_474`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_474_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_474_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.475. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_475
- **Tên bảng vật lý**: `fea_data_schema_table_475`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_475_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_475_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.476. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_476
- **Tên bảng vật lý**: `fea_data_schema_table_476`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_476_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_476_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.477. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_477
- **Tên bảng vật lý**: `fea_data_schema_table_477`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_477_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_477_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.478. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_478
- **Tên bảng vật lý**: `fea_data_schema_table_478`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_478_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_478_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.479. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_479
- **Tên bảng vật lý**: `fea_data_schema_table_479`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_479_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_479_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.480. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_480
- **Tên bảng vật lý**: `fea_data_schema_table_480`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_480_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_480_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.481. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_481
- **Tên bảng vật lý**: `fea_data_schema_table_481`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_481_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_481_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.482. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_482
- **Tên bảng vật lý**: `fea_data_schema_table_482`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_482_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_482_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.483. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_483
- **Tên bảng vật lý**: `fea_data_schema_table_483`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_483_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_483_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.484. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_484
- **Tên bảng vật lý**: `fea_data_schema_table_484`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_484_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_484_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.485. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_485
- **Tên bảng vật lý**: `fea_data_schema_table_485`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_485_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_485_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.486. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_486
- **Tên bảng vật lý**: `fea_data_schema_table_486`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_486_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_486_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.487. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_487
- **Tên bảng vật lý**: `fea_data_schema_table_487`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_487_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_487_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.488. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_488
- **Tên bảng vật lý**: `fea_data_schema_table_488`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_488_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_488_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.489. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_489
- **Tên bảng vật lý**: `fea_data_schema_table_489`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_489_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_489_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.490. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_490
- **Tên bảng vật lý**: `fea_data_schema_table_490`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_490_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_490_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.491. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_491
- **Tên bảng vật lý**: `fea_data_schema_table_491`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_491_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_491_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.492. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_492
- **Tên bảng vật lý**: `fea_data_schema_table_492`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_492_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_492_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.493. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_493
- **Tên bảng vật lý**: `fea_data_schema_table_493`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_493_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_493_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.494. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_494
- **Tên bảng vật lý**: `fea_data_schema_table_494`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_494_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_494_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.495. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_495
- **Tên bảng vật lý**: `fea_data_schema_table_495`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_495_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_495_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.496. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_496
- **Tên bảng vật lý**: `fea_data_schema_table_496`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_496_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_496_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.497. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_497
- **Tên bảng vật lý**: `fea_data_schema_table_497`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_497_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_497_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.498. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_498
- **Tên bảng vật lý**: `fea_data_schema_table_498`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_498_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_498_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.499. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_499
- **Tên bảng vật lý**: `fea_data_schema_table_499`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_499_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_499_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.500. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_500
- **Tên bảng vật lý**: `fea_data_schema_table_500`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_500_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_500_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.501. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_501
- **Tên bảng vật lý**: `fea_data_schema_table_501`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_501_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_501_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.502. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_502
- **Tên bảng vật lý**: `fea_data_schema_table_502`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_502_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_502_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.503. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_503
- **Tên bảng vật lý**: `fea_data_schema_table_503`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_503_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_503_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.504. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_504
- **Tên bảng vật lý**: `fea_data_schema_table_504`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_504_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_504_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.505. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_505
- **Tên bảng vật lý**: `fea_data_schema_table_505`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_505_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_505_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.506. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_506
- **Tên bảng vật lý**: `fea_data_schema_table_506`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_506_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_506_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.507. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_507
- **Tên bảng vật lý**: `fea_data_schema_table_507`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_507_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_507_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.508. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_508
- **Tên bảng vật lý**: `fea_data_schema_table_508`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_508_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_508_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.509. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_509
- **Tên bảng vật lý**: `fea_data_schema_table_509`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_509_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_509_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.510. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_510
- **Tên bảng vật lý**: `fea_data_schema_table_510`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_510_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_510_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.511. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_511
- **Tên bảng vật lý**: `fea_data_schema_table_511`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_511_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_511_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.512. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_512
- **Tên bảng vật lý**: `fea_data_schema_table_512`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_512_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_512_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.513. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_513
- **Tên bảng vật lý**: `fea_data_schema_table_513`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_513_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_513_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.514. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_514
- **Tên bảng vật lý**: `fea_data_schema_table_514`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_514_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_514_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.515. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_515
- **Tên bảng vật lý**: `fea_data_schema_table_515`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_515_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_515_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.516. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_516
- **Tên bảng vật lý**: `fea_data_schema_table_516`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_516_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_516_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.517. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_517
- **Tên bảng vật lý**: `fea_data_schema_table_517`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_517_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_517_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.518. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_518
- **Tên bảng vật lý**: `fea_data_schema_table_518`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_518_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_518_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.519. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_519
- **Tên bảng vật lý**: `fea_data_schema_table_519`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_519_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_519_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.520. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_520
- **Tên bảng vật lý**: `fea_data_schema_table_520`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_520_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_520_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.521. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_521
- **Tên bảng vật lý**: `fea_data_schema_table_521`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_521_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_521_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.522. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_522
- **Tên bảng vật lý**: `fea_data_schema_table_522`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_522_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_522_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.523. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_523
- **Tên bảng vật lý**: `fea_data_schema_table_523`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_523_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_523_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.524. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_524
- **Tên bảng vật lý**: `fea_data_schema_table_524`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_524_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_524_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.525. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_525
- **Tên bảng vật lý**: `fea_data_schema_table_525`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_525_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_525_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.526. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_526
- **Tên bảng vật lý**: `fea_data_schema_table_526`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_526_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_526_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.527. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_527
- **Tên bảng vật lý**: `fea_data_schema_table_527`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_527_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_527_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.528. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_528
- **Tên bảng vật lý**: `fea_data_schema_table_528`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_528_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_528_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.529. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_529
- **Tên bảng vật lý**: `fea_data_schema_table_529`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_529_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_529_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.530. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_530
- **Tên bảng vật lý**: `fea_data_schema_table_530`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_530_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_530_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.531. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_531
- **Tên bảng vật lý**: `fea_data_schema_table_531`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_531_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_531_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.532. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_532
- **Tên bảng vật lý**: `fea_data_schema_table_532`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_532_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_532_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.533. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_533
- **Tên bảng vật lý**: `fea_data_schema_table_533`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_533_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_533_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.534. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_534
- **Tên bảng vật lý**: `fea_data_schema_table_534`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_534_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_534_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.535. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_535
- **Tên bảng vật lý**: `fea_data_schema_table_535`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_535_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_535_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.536. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_536
- **Tên bảng vật lý**: `fea_data_schema_table_536`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_536_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_536_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.537. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_537
- **Tên bảng vật lý**: `fea_data_schema_table_537`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_537_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_537_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.538. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_538
- **Tên bảng vật lý**: `fea_data_schema_table_538`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_538_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_538_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.539. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_539
- **Tên bảng vật lý**: `fea_data_schema_table_539`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_539_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_539_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.540. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_540
- **Tên bảng vật lý**: `fea_data_schema_table_540`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_540_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_540_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.541. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_541
- **Tên bảng vật lý**: `fea_data_schema_table_541`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_541_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_541_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.542. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_542
- **Tên bảng vật lý**: `fea_data_schema_table_542`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_542_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_542_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.543. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_543
- **Tên bảng vật lý**: `fea_data_schema_table_543`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_543_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_543_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.544. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_544
- **Tên bảng vật lý**: `fea_data_schema_table_544`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_544_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_544_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.545. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_545
- **Tên bảng vật lý**: `fea_data_schema_table_545`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_545_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_545_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.546. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_546
- **Tên bảng vật lý**: `fea_data_schema_table_546`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_546_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_546_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.547. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_547
- **Tên bảng vật lý**: `fea_data_schema_table_547`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_547_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_547_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.548. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_548
- **Tên bảng vật lý**: `fea_data_schema_table_548`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_548_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_548_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.549. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_549
- **Tên bảng vật lý**: `fea_data_schema_table_549`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_549_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_549_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.550. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_550
- **Tên bảng vật lý**: `fea_data_schema_table_550`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_550_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_550_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.551. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_551
- **Tên bảng vật lý**: `fea_data_schema_table_551`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_551_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_551_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.552. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_552
- **Tên bảng vật lý**: `fea_data_schema_table_552`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_552_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_552_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.553. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_553
- **Tên bảng vật lý**: `fea_data_schema_table_553`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_553_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_553_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.554. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_554
- **Tên bảng vật lý**: `fea_data_schema_table_554`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_554_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_554_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.555. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_555
- **Tên bảng vật lý**: `fea_data_schema_table_555`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_555_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_555_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.556. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_556
- **Tên bảng vật lý**: `fea_data_schema_table_556`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_556_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_556_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.557. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_557
- **Tên bảng vật lý**: `fea_data_schema_table_557`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_557_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_557_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.558. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_558
- **Tên bảng vật lý**: `fea_data_schema_table_558`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_558_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_558_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.559. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_559
- **Tên bảng vật lý**: `fea_data_schema_table_559`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_559_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_559_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.560. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_560
- **Tên bảng vật lý**: `fea_data_schema_table_560`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_560_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_560_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.561. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_561
- **Tên bảng vật lý**: `fea_data_schema_table_561`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_561_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_561_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.562. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_562
- **Tên bảng vật lý**: `fea_data_schema_table_562`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_562_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_562_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.563. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_563
- **Tên bảng vật lý**: `fea_data_schema_table_563`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_563_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_563_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.564. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_564
- **Tên bảng vật lý**: `fea_data_schema_table_564`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_564_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_564_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.565. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_565
- **Tên bảng vật lý**: `fea_data_schema_table_565`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_565_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_565_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.566. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_566
- **Tên bảng vật lý**: `fea_data_schema_table_566`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_566_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_566_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.567. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_567
- **Tên bảng vật lý**: `fea_data_schema_table_567`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_567_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_567_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.568. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_568
- **Tên bảng vật lý**: `fea_data_schema_table_568`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_568_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_568_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.569. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_569
- **Tên bảng vật lý**: `fea_data_schema_table_569`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_569_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_569_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.570. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_570
- **Tên bảng vật lý**: `fea_data_schema_table_570`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_570_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_570_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.571. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_571
- **Tên bảng vật lý**: `fea_data_schema_table_571`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_571_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_571_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.572. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_572
- **Tên bảng vật lý**: `fea_data_schema_table_572`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_572_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_572_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.573. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_573
- **Tên bảng vật lý**: `fea_data_schema_table_573`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_573_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_573_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.574. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_574
- **Tên bảng vật lý**: `fea_data_schema_table_574`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_574_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_574_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.575. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_575
- **Tên bảng vật lý**: `fea_data_schema_table_575`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_575_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_575_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.576. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_576
- **Tên bảng vật lý**: `fea_data_schema_table_576`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_576_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_576_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.577. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_577
- **Tên bảng vật lý**: `fea_data_schema_table_577`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_577_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_577_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.578. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_578
- **Tên bảng vật lý**: `fea_data_schema_table_578`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_578_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_578_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.579. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_579
- **Tên bảng vật lý**: `fea_data_schema_table_579`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_579_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_579_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.580. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_580
- **Tên bảng vật lý**: `fea_data_schema_table_580`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_580_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_580_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.581. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_581
- **Tên bảng vật lý**: `fea_data_schema_table_581`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_581_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_581_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.582. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_582
- **Tên bảng vật lý**: `fea_data_schema_table_582`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_582_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_582_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.583. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_583
- **Tên bảng vật lý**: `fea_data_schema_table_583`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_583_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_583_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.584. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_584
- **Tên bảng vật lý**: `fea_data_schema_table_584`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_584_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_584_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.585. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_585
- **Tên bảng vật lý**: `fea_data_schema_table_585`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_585_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_585_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.586. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_586
- **Tên bảng vật lý**: `fea_data_schema_table_586`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_586_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_586_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.587. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_587
- **Tên bảng vật lý**: `fea_data_schema_table_587`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_587_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_587_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.588. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_588
- **Tên bảng vật lý**: `fea_data_schema_table_588`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_588_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_588_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.589. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_589
- **Tên bảng vật lý**: `fea_data_schema_table_589`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_589_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_589_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.590. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_590
- **Tên bảng vật lý**: `fea_data_schema_table_590`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_590_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_590_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.591. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_591
- **Tên bảng vật lý**: `fea_data_schema_table_591`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_591_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_591_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.592. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_592
- **Tên bảng vật lý**: `fea_data_schema_table_592`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_592_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_592_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.593. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_593
- **Tên bảng vật lý**: `fea_data_schema_table_593`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_593_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_593_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.594. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_594
- **Tên bảng vật lý**: `fea_data_schema_table_594`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_594_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_594_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.595. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_595
- **Tên bảng vật lý**: `fea_data_schema_table_595`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_595_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_595_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.596. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_596
- **Tên bảng vật lý**: `fea_data_schema_table_596`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_596_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_596_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.597. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_597
- **Tên bảng vật lý**: `fea_data_schema_table_597`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_597_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_597_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.598. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_598
- **Tên bảng vật lý**: `fea_data_schema_table_598`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_598_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_598_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.599. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_599
- **Tên bảng vật lý**: `fea_data_schema_table_599`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_599_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_599_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.600. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_600
- **Tên bảng vật lý**: `fea_data_schema_table_600`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_600_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_600_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.601. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_601
- **Tên bảng vật lý**: `fea_data_schema_table_601`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_601_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_601_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.602. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_602
- **Tên bảng vật lý**: `fea_data_schema_table_602`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_602_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_602_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.603. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_603
- **Tên bảng vật lý**: `fea_data_schema_table_603`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_603_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_603_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.604. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_604
- **Tên bảng vật lý**: `fea_data_schema_table_604`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_604_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_604_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.605. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_605
- **Tên bảng vật lý**: `fea_data_schema_table_605`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_605_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_605_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.606. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_606
- **Tên bảng vật lý**: `fea_data_schema_table_606`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_606_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_606_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.607. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_607
- **Tên bảng vật lý**: `fea_data_schema_table_607`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_607_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_607_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.608. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_608
- **Tên bảng vật lý**: `fea_data_schema_table_608`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_608_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_608_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.609. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_609
- **Tên bảng vật lý**: `fea_data_schema_table_609`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_609_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_609_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.610. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_610
- **Tên bảng vật lý**: `fea_data_schema_table_610`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_610_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_610_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.611. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_611
- **Tên bảng vật lý**: `fea_data_schema_table_611`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_611_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_611_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.612. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_612
- **Tên bảng vật lý**: `fea_data_schema_table_612`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_612_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_612_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.613. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_613
- **Tên bảng vật lý**: `fea_data_schema_table_613`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_613_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_613_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.614. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_614
- **Tên bảng vật lý**: `fea_data_schema_table_614`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_614_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_614_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.615. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_615
- **Tên bảng vật lý**: `fea_data_schema_table_615`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_615_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_615_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.616. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_616
- **Tên bảng vật lý**: `fea_data_schema_table_616`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_616_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_616_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.617. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_617
- **Tên bảng vật lý**: `fea_data_schema_table_617`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_617_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_617_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.618. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_618
- **Tên bảng vật lý**: `fea_data_schema_table_618`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_618_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_618_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.619. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_619
- **Tên bảng vật lý**: `fea_data_schema_table_619`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_619_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_619_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.620. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_620
- **Tên bảng vật lý**: `fea_data_schema_table_620`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_620_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_620_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.621. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_621
- **Tên bảng vật lý**: `fea_data_schema_table_621`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_621_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_621_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.622. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_622
- **Tên bảng vật lý**: `fea_data_schema_table_622`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_622_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_622_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.623. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_623
- **Tên bảng vật lý**: `fea_data_schema_table_623`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_623_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_623_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.624. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_624
- **Tên bảng vật lý**: `fea_data_schema_table_624`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_624_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_624_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.625. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_625
- **Tên bảng vật lý**: `fea_data_schema_table_625`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_625_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_625_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.626. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_626
- **Tên bảng vật lý**: `fea_data_schema_table_626`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_626_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_626_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.627. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_627
- **Tên bảng vật lý**: `fea_data_schema_table_627`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_627_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_627_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.628. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_628
- **Tên bảng vật lý**: `fea_data_schema_table_628`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_628_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_628_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.629. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_629
- **Tên bảng vật lý**: `fea_data_schema_table_629`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_629_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_629_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.630. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_630
- **Tên bảng vật lý**: `fea_data_schema_table_630`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_630_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_630_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.631. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_631
- **Tên bảng vật lý**: `fea_data_schema_table_631`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_631_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_631_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.632. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_632
- **Tên bảng vật lý**: `fea_data_schema_table_632`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_632_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_632_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.633. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_633
- **Tên bảng vật lý**: `fea_data_schema_table_633`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_633_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_633_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.634. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_634
- **Tên bảng vật lý**: `fea_data_schema_table_634`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_634_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_634_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.635. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_635
- **Tên bảng vật lý**: `fea_data_schema_table_635`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_635_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_635_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.636. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_636
- **Tên bảng vật lý**: `fea_data_schema_table_636`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_636_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_636_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.637. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_637
- **Tên bảng vật lý**: `fea_data_schema_table_637`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_637_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_637_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.638. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_638
- **Tên bảng vật lý**: `fea_data_schema_table_638`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_638_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_638_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.639. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_639
- **Tên bảng vật lý**: `fea_data_schema_table_639`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_639_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_639_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.640. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_640
- **Tên bảng vật lý**: `fea_data_schema_table_640`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_640_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_640_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.641. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_641
- **Tên bảng vật lý**: `fea_data_schema_table_641`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_641_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_641_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.642. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_642
- **Tên bảng vật lý**: `fea_data_schema_table_642`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_642_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_642_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.643. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_643
- **Tên bảng vật lý**: `fea_data_schema_table_643`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_643_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_643_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.644. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_644
- **Tên bảng vật lý**: `fea_data_schema_table_644`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_644_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_644_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.645. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_645
- **Tên bảng vật lý**: `fea_data_schema_table_645`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_645_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_645_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.646. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_646
- **Tên bảng vật lý**: `fea_data_schema_table_646`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_646_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_646_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.647. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_647
- **Tên bảng vật lý**: `fea_data_schema_table_647`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_647_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_647_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.648. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_648
- **Tên bảng vật lý**: `fea_data_schema_table_648`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_648_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_648_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.649. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_649
- **Tên bảng vật lý**: `fea_data_schema_table_649`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_649_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_649_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.650. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_650
- **Tên bảng vật lý**: `fea_data_schema_table_650`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_650_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_650_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.651. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_651
- **Tên bảng vật lý**: `fea_data_schema_table_651`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_651_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_651_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.652. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_652
- **Tên bảng vật lý**: `fea_data_schema_table_652`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_652_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_652_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.653. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_653
- **Tên bảng vật lý**: `fea_data_schema_table_653`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_653_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_653_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.654. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_654
- **Tên bảng vật lý**: `fea_data_schema_table_654`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_654_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_654_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.655. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_655
- **Tên bảng vật lý**: `fea_data_schema_table_655`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_655_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_655_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.656. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_656
- **Tên bảng vật lý**: `fea_data_schema_table_656`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_656_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_656_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.657. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_657
- **Tên bảng vật lý**: `fea_data_schema_table_657`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_657_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_657_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.658. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_658
- **Tên bảng vật lý**: `fea_data_schema_table_658`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_658_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_658_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.659. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_659
- **Tên bảng vật lý**: `fea_data_schema_table_659`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_659_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_659_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.660. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_660
- **Tên bảng vật lý**: `fea_data_schema_table_660`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_660_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_660_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.661. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_661
- **Tên bảng vật lý**: `fea_data_schema_table_661`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_661_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_661_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.662. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_662
- **Tên bảng vật lý**: `fea_data_schema_table_662`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_662_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_662_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.663. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_663
- **Tên bảng vật lý**: `fea_data_schema_table_663`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_663_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_663_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.664. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_664
- **Tên bảng vật lý**: `fea_data_schema_table_664`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_664_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_664_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.665. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_665
- **Tên bảng vật lý**: `fea_data_schema_table_665`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_665_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_665_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.666. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_666
- **Tên bảng vật lý**: `fea_data_schema_table_666`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_666_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_666_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.667. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_667
- **Tên bảng vật lý**: `fea_data_schema_table_667`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_667_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_667_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.668. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_668
- **Tên bảng vật lý**: `fea_data_schema_table_668`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_668_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_668_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.669. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_669
- **Tên bảng vật lý**: `fea_data_schema_table_669`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_669_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_669_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.670. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_670
- **Tên bảng vật lý**: `fea_data_schema_table_670`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_670_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_670_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.671. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_671
- **Tên bảng vật lý**: `fea_data_schema_table_671`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_671_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_671_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.672. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_672
- **Tên bảng vật lý**: `fea_data_schema_table_672`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_672_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_672_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.673. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_673
- **Tên bảng vật lý**: `fea_data_schema_table_673`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_673_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_673_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.674. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_674
- **Tên bảng vật lý**: `fea_data_schema_table_674`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_674_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_674_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.675. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_675
- **Tên bảng vật lý**: `fea_data_schema_table_675`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_675_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_675_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.676. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_676
- **Tên bảng vật lý**: `fea_data_schema_table_676`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_676_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_676_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.677. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_677
- **Tên bảng vật lý**: `fea_data_schema_table_677`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_677_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_677_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.678. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_678
- **Tên bảng vật lý**: `fea_data_schema_table_678`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_678_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_678_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.679. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_679
- **Tên bảng vật lý**: `fea_data_schema_table_679`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_679_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_679_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.680. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_680
- **Tên bảng vật lý**: `fea_data_schema_table_680`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_680_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_680_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.681. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_681
- **Tên bảng vật lý**: `fea_data_schema_table_681`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_681_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_681_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.682. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_682
- **Tên bảng vật lý**: `fea_data_schema_table_682`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_682_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_682_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.683. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_683
- **Tên bảng vật lý**: `fea_data_schema_table_683`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_683_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_683_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.684. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_684
- **Tên bảng vật lý**: `fea_data_schema_table_684`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_684_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_684_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.685. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_685
- **Tên bảng vật lý**: `fea_data_schema_table_685`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_685_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_685_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.686. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_686
- **Tên bảng vật lý**: `fea_data_schema_table_686`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_686_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_686_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.687. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_687
- **Tên bảng vật lý**: `fea_data_schema_table_687`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_687_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_687_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.688. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_688
- **Tên bảng vật lý**: `fea_data_schema_table_688`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_688_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_688_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.689. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_689
- **Tên bảng vật lý**: `fea_data_schema_table_689`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_689_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_689_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.690. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_690
- **Tên bảng vật lý**: `fea_data_schema_table_690`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_690_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_690_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.691. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_691
- **Tên bảng vật lý**: `fea_data_schema_table_691`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_691_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_691_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.692. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_692
- **Tên bảng vật lý**: `fea_data_schema_table_692`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_692_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_692_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.693. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_693
- **Tên bảng vật lý**: `fea_data_schema_table_693`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_693_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_693_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.694. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_694
- **Tên bảng vật lý**: `fea_data_schema_table_694`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_694_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_694_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.695. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_695
- **Tên bảng vật lý**: `fea_data_schema_table_695`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_695_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_695_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.696. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_696
- **Tên bảng vật lý**: `fea_data_schema_table_696`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_696_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_696_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.697. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_697
- **Tên bảng vật lý**: `fea_data_schema_table_697`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_697_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_697_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.698. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_698
- **Tên bảng vật lý**: `fea_data_schema_table_698`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_698_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_698_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.699. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_699
- **Tên bảng vật lý**: `fea_data_schema_table_699`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_699_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_699_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.700. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_700
- **Tên bảng vật lý**: `fea_data_schema_table_700`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_700_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_700_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.701. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_701
- **Tên bảng vật lý**: `fea_data_schema_table_701`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_701_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_701_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.702. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_702
- **Tên bảng vật lý**: `fea_data_schema_table_702`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_702_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_702_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.703. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_703
- **Tên bảng vật lý**: `fea_data_schema_table_703`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_703_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_703_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.704. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_704
- **Tên bảng vật lý**: `fea_data_schema_table_704`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_704_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_704_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.705. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_705
- **Tên bảng vật lý**: `fea_data_schema_table_705`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_705_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_705_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.706. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_706
- **Tên bảng vật lý**: `fea_data_schema_table_706`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_706_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_706_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.707. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_707
- **Tên bảng vật lý**: `fea_data_schema_table_707`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_707_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_707_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.708. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_708
- **Tên bảng vật lý**: `fea_data_schema_table_708`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_708_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_708_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.709. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_709
- **Tên bảng vật lý**: `fea_data_schema_table_709`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_709_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_709_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.710. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_710
- **Tên bảng vật lý**: `fea_data_schema_table_710`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_710_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_710_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.711. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_711
- **Tên bảng vật lý**: `fea_data_schema_table_711`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_711_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_711_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.712. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_712
- **Tên bảng vật lý**: `fea_data_schema_table_712`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_712_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_712_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.713. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_713
- **Tên bảng vật lý**: `fea_data_schema_table_713`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_713_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_713_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.714. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_714
- **Tên bảng vật lý**: `fea_data_schema_table_714`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_714_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_714_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.715. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_715
- **Tên bảng vật lý**: `fea_data_schema_table_715`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_715_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_715_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.716. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_716
- **Tên bảng vật lý**: `fea_data_schema_table_716`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_716_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_716_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.717. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_717
- **Tên bảng vật lý**: `fea_data_schema_table_717`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_717_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_717_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.718. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_718
- **Tên bảng vật lý**: `fea_data_schema_table_718`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_718_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_718_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.719. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_719
- **Tên bảng vật lý**: `fea_data_schema_table_719`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_719_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_719_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.

### 3.720. Bảng Thực thể & Lược đồ Dữ liệu Table_Schema_720
- **Tên bảng vật lý**: `fea_data_schema_table_720`
- **Các cột chính**: `id (BIGINT UNSIGNED, PK, AUTO_INCREMENT)`, `uuid (CHAR(36), UNIQUE)`, `user_id (BIGINT UNSIGNED, FK -> users.id)`, `status (ENUM, DEFAULT 'active')`, `metadata (JSON)`, `created_at (TIMESTAMP)`, `updated_at (TIMESTAMP)`.
- **Khóa ngoại & Ràng buộc**: `CONSTRAINT fk_schema_720_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE`.
- **Chỉ mục lập sẵn**: `INDEX idx_schema_720_lookup (user_id, status, created_at)`.
- **Engine lưu trữ**: `InnoDB`, Character Set: `utf8mb4`, Collation: `utf8mb4_unicode_ci`.
- **Chiến lược phân vùng**: Partitioning theo khoảng thời gian năm vận hành nhằm tối ưu hóa bảng dữ liệu lớn.
- **Quy tắc an toàn**: Hỗ trợ Soft Deletes với `deleted_at (TIMESTAMP NULL)` phục vụ truy vết dữ liệu.
