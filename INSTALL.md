# SimpleLicManager Kurulum Rehberi

## Local Kurulum

### 1. Sistem Gereksinimleri
- PHP >= 8.2
- MySQL 8+
- Composer
- Git

### 2. Kurulum Adımları

```bash
# 1. Projeyi klonlayın
git clone https://github.com/AhmetNuri/SimpleLicManager.git
cd SimpleLicManager

# 2. Bağımlılıkları yükleyin
composer install

# 3. .env dosyasını oluşturun
cp .env.example .env

# 4. Uygulama anahtarını oluşturun
php artisan key:generate

# 5. Veritabanını ayarlayın (.env dosyasını düzenleyin)
# DB_DATABASE=simple_lic_manager
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 6. Veritabanını oluşturun
mysql -u root -p -e "CREATE DATABASE simple_lic_manager;"

# 7. Migration ve seed'leri çalıştırın
php artisan migrate --seed

# 8. Sunucuyu başlatın
php artisan serve
```

Uygulama http://localhost:8000 adresinde çalışacaktır.

**Varsayılan Kullanıcılar:**
- Admin: admin@example.com / password
- Demo: demo@example.com / password

---

## cPanel Hosting Kurulumu

### 1. Local'de Hazırlık

```bash
# Production bağımlılıkları yükleyin
composer install --no-dev --optimize-autoloader

# Cache'leri oluşturun
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 2. Dosya Yükleme

1. Tüm proje dosyalarını cPanel File Manager ile yükleyin
2. Ana laravel klasörünü `public_html` dışına yerleştirin (örn: `/home/username/laravel`)
3. `public` klasörünün içeriğini `public_html`'e kopyalayın

### 3. Index.php Düzenleme

`public_html/index.php` dosyasını düzenleyin:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Laravel root path'ini ayarlayın
require __DIR__.'/../laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

// Geri kalan kod aynı kalır...
```

### 4. .env Ayarları

cPanel File Manager ile `.env` dosyası oluşturun:

```env
APP_NAME=SimpleLicManager
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password

# ... diğer ayarlar
```

### 5. Veritabanı Kurulumu

cPanel MySQL Database bölümünden:
1. Veritabanı oluşturun
2. Kullanıcı oluşturun ve yetkiler verin
3. Terminal erişimi varsa migration'ları çalıştırın:

```bash
cd /home/username/laravel
php artisan migrate --seed
```

**Terminal erişimi yoksa:**
- PHPMyAdmin'den SQL dosyası import edin
- Veya local'de SQL export alıp yükleyin

### 6. İzin Ayarları

```bash
chmod -R 755 /home/username/laravel/storage
chmod -R 755 /home/username/laravel/bootstrap/cache
```

### 7. .htaccess Kontrolü

`public_html/.htaccess` dosyasının doğru olduğundan emin olun:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

## Test

### API Test

```bash
curl -X POST https://yourdomain.com/api/v1/license/check \
  -H "Content-Type: application/json" \
  -d '{"email":"demo@example.com","serial_number":"DEMO-1234-5678-ABCD"}'
```

### Web Arayüz Test

1. https://yourdomain.com adresine gidin
2. Admin kullanıcısı ile giriş yapın
3. Kullanıcı ve lisans yönetimini test edin

---

## Sorun Giderme

### 500 Internal Server Error

```bash
# Log dosyalarını kontrol edin
tail -f storage/logs/laravel.log

# Cache'leri temizleyin
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Permission Denied

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Database Connection Error

- `.env` dosyasındaki veritabanı bilgilerini kontrol edin
- cPanel'de veritabanı kullanıcısına yetki verildiğinden emin olun

---

## Güvenlik Notları

1. Production'da `APP_DEBUG=false` yapın
2. `.env` dosyasını asla Git'e eklemeyin
3. Güçlü `APP_KEY` kullanın
4. Düzenli olarak yedek alın
5. HTTPS kullanın
6. Varsayılan şifreleri değiştirin

---

## Destek

Sorunlarınız için GitHub Issues kullanın:
https://github.com/AhmetNuri/SimpleLicManager/issues
