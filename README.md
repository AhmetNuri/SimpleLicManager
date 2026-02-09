# SimpleLicManager

Laravel 12 tabanlı SaaS Lisanslama Yönetim Sistemi.

## Özellikler

- **RESTful API** - Delphi VCL REST Client uyumlu
- **Kullanıcı Yönetimi** - Admin panelden kullanıcı CRUD
- **Lisans Yönetimi** - Benzersiz seri numaraları, farklı lisans tipleri
- **Cihaz Bağlama** - Hardware fingerprint ile cihaz kontrolü
- **Log Sistemi** - Detaylı loglama (info, debug, error)
- **Rate Limiting** - API koruması
- **Admin Paneli** - Kullanıcı ve lisans yönetimi
- **Kullanıcı Paneli** - Lisans görüntüleme ve şifre değiştirme

## Gereksinimler

- PHP >= 8.2
- MySQL 8+
- Composer (sadece local geliştirme için)

## Kurulum (Local Geliştirme)

1. **Projeyi Klonlayın**
```bash
git clone https://github.com/AhmetNuri/SimpleLicManager.git
cd SimpleLicManager
```

2. **Bağımlılıkları Yükleyin**
```bash
composer install
```

3. **Environment Dosyasını Oluşturun**
```bash
cp .env.example .env
```

4. **Veritabanı Ayarlarını Yapılandırın**
`.env` dosyasını düzenleyin:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simple_lic_manager
DB_USERNAME=root
DB_PASSWORD=your_password
```

5. **Uygulama Anahtarını Oluşturun**
```bash
php artisan key:generate
```

6. **Veritabanını Oluşturun**
```bash
mysql -u root -p
CREATE DATABASE simple_lic_manager;
exit;
```

7. **Migration'ları Çalıştırın**
```bash
php artisan migrate
```

8. **Seed Verilerini Yükleyin**
```bash
php artisan db:seed
```

9. **Geliştirme Sunucusunu Başlatın**
```bash
php artisan serve
```

Uygulama http://localhost:8000 adresinde çalışacaktır.

## Varsayılan Kullanıcılar

- **Admin:** admin@example.com / password
- **Demo:** demo@example.com / password

## cPanel Hosting'e Dağıtım

1. **Production Build Hazırlayın**
```bash
# Composer optimizasyonu
composer install --no-dev --optimize-autoloader

# Config cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Public dosyaları optimize et
php artisan optimize
```

2. **Dosyaları cPanel'e Yükleyin**
- Tüm dosyaları sunucuya FTP/SFTP ile yükleyin
- `public` klasörünün içeriğini `public_html` veya `www` klasörüne taşıyın
- Ana Laravel klasörünü public_html'in dışında tutun (örn: `/home/username/laravel`)

3. **public_html/index.php Düzenleyin**
```php
require __DIR__.'/../laravel/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel/bootstrap/app.php';
```

4. **.env Dosyasını Yapılandırın**
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

5. **Veritabanını Kurun**
cPanel'den MySQL veritabanı oluşturun ve migration'ları çalıştırın.

## API Kullanımı

### Lisans Kontrolü

**Endpoint:** `POST /api/v1/license/check`

**Request Body:**
```json
{
  "email": "demo@example.com",
  "serial_number": "DEMO-1234-5678-ABCD",
  "device_id": "optional-device-fingerprint"
}
```

**Başarılı Response:**
```json
{
  "valid": true,
  "package": "ModalMasterPro",
  "type": "yearly",
  "emergency": false,
  "expires_at": "2026-08-15",
  "days_left": 8,
  "warning": "Lisansınızın bitmesine 8 günden az kaldı!"
}
```

**Hatalı Response:**
```json
{
  "valid": false,
  "message": "Lisans bulunamadı / süresi doldu / cihaz eşleşmedi"
}
```

### Rate Limiting
API endpoint'i dakikada 60 istek ile sınırlıdır.

## Delphi REST Client Örneği

```pascal
procedure TForm1.CheckLicense;
var
  RESTClient: TRESTClient;
  RESTRequest: TRESTRequest;
  RESTResponse: TRESTResponse;
  JSONObj: TJSONObject;
begin
  RESTClient := TRESTClient.Create('http://yourdomain.com/api/v1');
  RESTRequest := TRESTRequest.Create(nil);
  RESTResponse := TRESTResponse.Create(nil);
  
  try
    RESTRequest.Client := RESTClient;
    RESTRequest.Response := RESTResponse;
    RESTRequest.Resource := 'license/check';
    RESTRequest.Method := rmPOST;
    
    JSONObj := TJSONObject.Create;
    try
      JSONObj.AddPair('email', 'demo@example.com');
      JSONObj.AddPair('serial_number', 'DEMO-1234-5678-ABCD');
      JSONObj.AddPair('device_id', GetDeviceID); // Kendi fonksiyonunuz
      
      RESTRequest.AddBody(JSONObj.ToString, ctAPPLICATION_JSON);
      RESTRequest.Execute;
      
      if RESTResponse.StatusCode = 200 then
      begin
        // Başarılı
        ShowMessage('Lisans geçerli!');
      end
      else
      begin
        // Hata
        ShowMessage('Lisans geçersiz!');
      end;
    finally
      JSONObj.Free;
    end;
  finally
    RESTClient.Free;
    RESTRequest.Free;
    RESTResponse.Free;
  end;
end;
```

## Veritabanı Yapısı

### users
- id, email, password, remember_token, timestamps

### licenses
- id, user_id, device_id, serial_number (unique), starts_at, last_checked_date
- last_checked_device_id, emergency, expires_at, license_type, product_package
- user_enable, max_connection_count, timestamps

### lic_logs
- id, license_id, user_id, level (info/debug/error), message, created_at

## Lisans Tipleri

- **demo** - Demo lisans
- **monthly** - Aylık lisans
- **yearly** - Yıllık lisans
- **lifetime** - Ömür boyu lisans (expires_at = null)

## Güvenlik

- Tüm şifreler bcrypt ile hash'lenir
- API rate limiting aktif (60 req/min)
- CSRF koruması aktif
- Session güvenliği
- SQL Injection koruması (Eloquent ORM)

## Lisans

MIT License

## Destek

Sorularınız için issue açabilirsiniz.
