# Hướng dẫn cấu hình MySQL cho dự án

## Bước 1: Đảm bảo MySQL đang chạy

MySQL service đã được kiểm tra và đang chạy (MySQL80).

## Bước 2: Xác định port MySQL

Chạy script kiểm tra để tìm port MySQL:

```powershell
$env:PATH = "C:\php8.3;$env:PATH"
php test-mysql.php
```

**Lưu ý:** Nếu bạn dùng XAMPP, MySQL thường chạy trên port **3306**. Nếu bạn cài MySQL riêng, có thể là port **3307** hoặc port khác.

## Bước 3: Cấu hình file .env

Mở file `.env` và cập nhật các thông tin sau:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dacn
DB_USERNAME=root
DB_PASSWORD=
```

**Quan trọng:**
- `DB_PORT`: Thay đổi theo port MySQL của bạn (3306 cho XAMPP, 3307 cho MySQL riêng)
- `DB_PASSWORD`: Nếu MySQL có password, nhập vào đây

**Lưu ý:**
- `DB_DATABASE`: Tên database bạn muốn sử dụng (ví dụ: `dacn`)
- `DB_USERNAME`: Thường là `root` nếu dùng XAMPP hoặc MySQL mặc định
- `DB_PASSWORD`: Mật khẩu MySQL (để trống nếu không có)

## Bước 3: Tạo database

Mở MySQL Command Line hoặc phpMyAdmin và chạy:

```sql
CREATE DATABASE IF NOT EXISTS dacn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Hoặc sử dụng PowerShell:

```powershell
# Sử dụng PHP từ C:\php8.3
$env:PATH = "C:\php8.3;$env:PATH"

# Tạo database (nếu MySQL có password, thêm -p)
mysql -u root -e "CREATE DATABASE IF NOT EXISTS dacn CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Bước 4: Sử dụng PHP từ C:\php8.3

Để sử dụng PHP từ `C:\php8.3` thay vì Herd, bạn có 2 cách:

### Cách 1: Sử dụng script helper (tạm thời trong session hiện tại)

```powershell
.\use-php.ps1
php artisan migrate
```

### Cách 2: Thêm vào PATH vĩnh viễn (khuyến nghị)

1. Mở **System Properties** → **Environment Variables**
2. Trong **User variables** hoặc **System variables**, tìm biến `Path`
3. Thêm `C:\php8.3` vào đầu danh sách
4. Khởi động lại PowerShell/Terminal

Sau đó bạn có thể dùng `php` trực tiếp:

```powershell
php artisan migrate
php artisan serve
```

## Bước 5: Chạy migrations

```powershell
# Đảm bảo đang dùng PHP từ C:\php8.3
$env:PATH = "C:\php8.3;$env:PATH"

# Chạy migrations
php artisan migrate

# Chạy seeders (tạo dữ liệu mẫu và admin user)
php artisan db:seed --class=UserRoleSeeder
```

## Bước 6: Kiểm tra kết nối

```powershell
$env:PATH = "C:\php8.3;$env:PATH"
php artisan migrate:status
```

Nếu không có lỗi, bạn đã cấu hình thành công!

## Xử lý lỗi

### Lỗi: "No connection could be made"
- Kiểm tra MySQL service có đang chạy: `Get-Service MySQL80`
- Kiểm tra port MySQL (mặc định 3306)
- Kiểm tra username/password trong `.env`

### Lỗi: "Access denied"
- Kiểm tra username/password MySQL
- Đảm bảo user có quyền truy cập database

### Lỗi: "Unknown database"
- Tạo database trước khi chạy migrate
- Kiểm tra tên database trong `.env` đúng chưa

