# Panduan Instalasi EduSaaS

## Persyaratan Sistem

### Software
- **PHP** >= 8.2
- **Composer** >= 2.x
- **Node.js** >= 18.x & NPM >= 9.x
- **MySQL** >= 8.0 atau MariaDB >= 10.6
- **Redis** >= 6.x (untuk queue dan cache)
- **Git**

### Ekstensi PHP
- BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML, cURL, GD/Imagick, Redis

### Opsional
- **Supervisor** (untuk menjalankan queue worker di production)
- **Nginx** atau **Apache** (web server)

---

## Instalasi (Development)

### 1. Clone Repository

```bash
git clone <repository-url> edusaas
cd edusaas
```

### 2. Install Dependency PHP

```bash
composer install
```

### 3. Install Dependency JavaScript

```bash
npm install
```

### 4. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env` dan sesuaikan konfigurasi:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=edusaas
DB_USERNAME=root
DB_PASSWORD=

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 5. Buat Database

```bash
mysql -u root -p -e "CREATE DATABASE edusaas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 6. Migrasi Database

```bash
php artisan migrate
```

### 7. Jalankan Seeder

```bash
php artisan db:seed
```

Ini akan membuat:
- 3 Paket Langganan (Starter, Professional, Enterprise)
- 1 Super Admin (superadmin@edusaas.id / password)
- 1 Sekolah Demo (SMP Negeri 1 Demo) dengan data lengkap
- 150 siswa, 20 guru, data akademik, SPP, dll.

### 8. Link Storage

```bash
php artisan storage:link
```

### 9. Build Assets

```bash
npm run build
```

### 10. Jalankan Aplikasi

```bash
# Terminal 1 - Server
php artisan serve

# Terminal 2 - Queue Worker
php artisan queue:work redis

# Terminal 3 - Vite (development)
npm run dev
```

Akses aplikasi di: **http://localhost:8000**

---

## Login Demo

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@edusaas.id | password |
| Admin Sekolah | admin@smpn1demo.id | password |

Panel URL:
- Super Admin: `/super-admin`
- Admin Sekolah: `/school-admin`
- Guru: `/teacher`

---

## Konfigurasi Tambahan

### WhatsApp (Fonnte)

Daftar di [fonnte.com](https://fonnte.com), lalu isi:

```env
FONNTE_API_TOKEN=token_anda
```

### Payment Gateway - Midtrans

Daftar di [midtrans.com](https://midtrans.com), lalu isi:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxx
MIDTRANS_IS_PRODUCTION=false
```

### Payment Gateway - Xendit

Daftar di [xendit.co](https://xendit.co), lalu isi:

```env
XENDIT_SECRET_KEY=xnd_development_xxx
XENDIT_WEBHOOK_TOKEN=token_anda
```

---

## Deploy ke Production

### 1. Konfigurasi Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com
```

### 2. Optimasi

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache
php artisan filament:cache-components
npm run build
```

### 3. Konfigurasi Supervisor

Buat file `/etc/supervisor/conf.d/edusaas-worker.conf`:

```ini
[program:edusaas-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/edusaas/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/edusaas/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start edusaas-worker:*
```

### 4. Konfigurasi Nginx

```nginx
server {
    listen 80;
    server_name domain-anda.com;
    root /path/to/edusaas/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 50M;
}
```

### 5. SSL (Let's Encrypt)

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d domain-anda.com
```

### 6. Cron Job

```bash
* * * * * cd /path/to/edusaas && php artisan schedule:run >> /dev/null 2>&1
```

---

## Troubleshooting

### Permission Error
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Redis Connection Refused
```bash
sudo systemctl start redis
sudo systemctl enable redis
```

### Queue Tidak Berjalan
```bash
php artisan queue:restart
sudo supervisorctl restart edusaas-worker:*
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
