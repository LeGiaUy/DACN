# DACN - Dự án Web Application

Dự án web application được xây dựng bằng Laravel 12 với Inertia.js và Vue 3.

## Yêu cầu hệ thống

Trước khi bắt đầu, hãy đảm bảo máy tính của bạn đã cài đặt:

- **PHP** >= 8.2
- **Composer** (PHP package manager)
- **Node.js** >= 18.x và **npm** (hoặc **yarn**)
- **SQLite** (hoặc MySQL/PostgreSQL nếu muốn sử dụng)

## Hướng dẫn cài đặt

### Bước 1: Clone repository

```bash
git clone <repository-url>
cd DACN
```

### Bước 2: Cài đặt dependencies PHP

```bash
composer install
```

### Bước 3: Cấu hình môi trường

Tạo file `.env` từ file mẫu (nếu có) hoặc tạo mới:

```bash
# Trên Windows (PowerShell)
copy .env.example .env

# Trên Linux/Mac
cp .env.example .env
```

Nếu không có file `.env.example`, bạn có thể tạo file `.env` mới với nội dung cơ bản:

```env
APP_NAME=DACN
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

SESSION_DRIVER=database
SESSION_LIFETIME=120

LOG_CHANNEL=stack
LOG_LEVEL=debug
```

### Bước 4: Tạo Application Key

```bash
php artisan key:generate
```

### Bước 5: Tạo database SQLite (nếu chưa có)

```bash
# Trên Windows (PowerShell)
New-Item -ItemType File -Path database\database.sqlite -Force

# Trên Linux/Mac
touch database/database.sqlite
```

**Lưu ý:** Nếu bạn muốn sử dụng MySQL hoặc PostgreSQL, hãy cấu hình trong file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Bước 6: Chạy migrations

```bash
php artisan migrate
```

### Bước 7: Chạy seeders (tùy chọn)

Để tạo dữ liệu mẫu và tài khoản admin:

```bash
php artisan db:seed --class=AdminUserSeeder
```

**Thông tin đăng nhập mặc định:**
- Email: `admin@test.com`
- Password: `password`

### Bước 8: Cài đặt dependencies JavaScript

```bash
npm install
```

### Bước 9: Build assets

```bash
npm run build
```

Hoặc nếu muốn chạy ở chế độ development với hot reload:

```bash
npm run dev
```

### Bước 10: Tạo symbolic link cho storage (nếu cần)

```bash
php artisan storage:link
```

### Bước 11: Chạy ứng dụng

Mở terminal mới và chạy:

```bash
php artisan serve
```

Ứng dụng sẽ chạy tại: `http://localhost:8000`

**Lưu ý:** Nếu bạn đang chạy `npm run dev` ở một terminal, hãy mở terminal khác để chạy `php artisan serve`.

## Cài đặt nhanh (Tất cả trong một lệnh)

Nếu bạn muốn cài đặt tất cả trong một lệnh, có thể sử dụng script có sẵn:

```bash
composer run setup
```

Script này sẽ tự động:
- Cài đặt Composer dependencies
- Tạo file `.env` nếu chưa có
- Tạo application key
- Chạy migrations
- Cài đặt npm dependencies
- Build assets

## Chạy Development Server

Để chạy cả backend và frontend cùng lúc với hot reload:

```bash
composer run dev
```

Lệnh này sẽ chạy:
- Laravel server
- Queue worker
- Log viewer (Pail)
- Vite dev server

## Cấu trúc dự án

```
DACN/
├── app/                    # Application logic
│   ├── Http/
│   │   ├── Controllers/   # Controllers
│   │   └── Middleware/    # Middleware
│   └── Models/            # Eloquent models
├── database/
│   ├── migrations/        # Database migrations
│   └── seeders/          # Database seeders
├── resources/
│   ├── js/               # Vue.js components và pages
│   └── css/              # Stylesheets
├── routes/               # Route definitions
├── public/              # Public assets
└── config/              # Configuration files
```

## Troubleshooting

### Lỗi permission trên Linux/Mac

Nếu gặp lỗi về quyền truy cập, hãy chạy:

```bash
chmod -R 775 storage bootstrap/cache
```

### Lỗi "Class not found"

Chạy lại autoload:

```bash
composer dump-autoload
```

### Lỗi database

Nếu gặp lỗi về database, hãy xóa file database cũ và chạy lại migrations:

```bash
# Xóa database cũ (SQLite)
rm database/database.sqlite
touch database/database.sqlite

# Chạy lại migrations
php artisan migrate:fresh
php artisan db:seed --class=AdminUserSeeder
```

### Clear cache

Nếu gặp lỗi cache, hãy clear:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Công nghệ sử dụng

- **Backend:** Laravel 12
- **Frontend:** Vue 3 + Inertia.js
- **Styling:** Tailwind CSS
- **Build Tool:** Vite
- **Database:** SQLite (có thể chuyển sang MySQL/PostgreSQL)

## Tài liệu tham khảo

- [Laravel Documentation](https://laravel.com/docs)
- [Inertia.js Documentation](https://inertiajs.com)
- [Vue.js Documentation](https://vuejs.org)

## License

MIT License
