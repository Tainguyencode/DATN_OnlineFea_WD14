# SETUP_GUIDE.md

## Yêu cầu

- PHP 8.3+
- Composer
- Node.js 18+
- MySQL 8+ hoặc SQLite (khuyến nghị MySQL cho production)

## Cài đặt

```bash
composer install
copy .env.example .env
php artisan key:generate
```

Cấu hình `.env`:

```env
APP_URL=http://127.0.0.1:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
```

```bash
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

## Kiểm tra

```bash
php artisan optimize:clear
php artisan route:list
php artisan test
```

## OAuth

- Google: https://console.cloud.google.com/
- GitHub: https://github.com/settings/developers
- Redirect URI: `{APP_URL}/auth/{provider}/callback`
