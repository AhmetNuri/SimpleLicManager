 @echo off
REM SimpleLicManager - XAMPP ile Windows Kurulum Script
REM Bu script, SimpleLicManager projesinin XAMPP üzerinde otomatik kurulumunu yapar

REM Script'in bulunduğu dizine geç
cd /d "%~dp0"

echo ======================================
echo SimpleLicManager - XAMPP Kurulumu
echo ======================================
echo.

REM XAMPP ve Composer yolu ayarları
set "XAMPP_PATH=D:\XAMPP"
set "PHP_PATH=%XAMPP_PATH%\php"
set "MYSQL_PATH=%XAMPP_PATH%\mysql\bin"
set "COMPOSER_PATH=D:\composer"

REM XAMPP PHP kontrolü
if not exist "%PHP_PATH%\php.exe" (
    echo X Hata: XAMPP PHP bulunamadi!
    echo Beklenen konum: %PHP_PATH%
    echo.
    echo Lutfen XAMPP kurulumunu kontrol edin veya asagidaki degiskeni duzenleyin:
    echo set "XAMPP_PATH=D:\XAMPP"
    echo.
    pause
    exit /b 1
)

REM PATH'e XAMPP ve Composer ekle
set "PATH=%PHP_PATH%;%MYSQL_PATH%;%COMPOSER_PATH%;%PATH%"

REM Artisan dosyası kontrolü
if not exist "artisan" (
    echo X Hata: Bu script'i proje ana dizininde calistirin!
    echo Kullanim: cd SimpleLicManager ^&^& setup.bat
    echo.
    pause
    exit /b 1
)

echo ======================================
echo 1/8 - PHP Versiyonu Kontrol Ediliyor
echo ======================================
php -v
if errorlevel 1 (
    echo X PHP calistirilamadi!
    pause
    exit /b 1
)
echo.

REM PHP versiyon kontrolü (8.2+)
php -r "if (version_compare(PHP_VERSION, '8.2.0', '<')) { echo 'X PHP 8.2 veya ustune ihtiyac var! Mevcut: ' . PHP_VERSION . PHP_EOL; exit(1); } else { echo '✓ PHP versiyonu uygun: ' . PHP_VERSION . PHP_EOL; }"
if errorlevel 1 (
    echo.
    echo XAMPP'in guncel bir versiyonunu yukleyin.
    pause
    exit /b 1
)
echo.

echo ======================================
echo 2/8 - Gerekli PHP Eklentileri Kontrol Ediliyor
echo ======================================

REM Gerekli extension'ları kontrol et
set "EXTENSIONS=pdo_mysql mbstring openssl fileinfo tokenizer xml ctype json bcmath"

for %%E in (%EXTENSIONS%) do (
    php -r "if (!extension_loaded('%%E')) { echo 'X Uyari: %%E eklentisi etkin degil!' . PHP_EOL; exit(1); }" >nul 2>&1
    if errorlevel 1 (
        echo ! %%E eklentisi eksik veya devre disi
        echo   php.ini dosyasinda extension=%%E satirini etkinlestirin
        echo   Dosya konumu: %PHP_PATH%\php.ini
    ) else (
        echo ✓ %%E eklentisi mevcut
    )
)
echo.

echo ======================================
echo 3/8 - Composer Kontrol Ediliyor
echo ======================================

REM Composer'ı önce PATH'te ara
where composer >nul 2>&1
if not errorlevel 1 (
    set "COMPOSER_CMD=call composer"
    set "COMPOSER_CALL_PREFIX=call "
    echo ✓ Composer PATH'te bulundu
    call composer --version
    goto :composer_found
)

REM PATH'te yoksa belirtilen dizinde ara
if exist "%COMPOSER_PATH%\composer.bat" (
    set "COMPOSER_CMD=call %COMPOSER_PATH%\composer.bat"
    set "COMPOSER_CALL_PREFIX=call "
    echo ✓ Composer bulundu: %COMPOSER_PATH%\composer.bat
    call "%COMPOSER_PATH%\composer.bat" --version
    goto :composer_found
)

if exist "%COMPOSER_PATH%\composer.phar" (
    set "COMPOSER_CMD=%PHP_PATH%\php.exe %COMPOSER_PATH%\composer.phar"
    set "COMPOSER_CALL_PREFIX="
    echo ✓ Composer bulundu: %COMPOSER_PATH%\composer.phar
    %PHP_PATH%\php.exe "%COMPOSER_PATH%\composer.phar" --version
    goto :composer_found
)

REM Proje dizininde composer.phar var mı kontrol et
if exist "%~dp0composer.phar" (
    set "COMPOSER_CMD=%PHP_PATH%\php.exe %~dp0composer.phar"
    set "COMPOSER_CALL_PREFIX="
    echo ✓ Composer bulundu: %~dp0composer.phar
    %PHP_PATH%\php.exe "%~dp0composer.phar" --version
    goto :composer_found
)

REM Hiçbir yerde bulunamadı
echo ! Composer bulunamadi
echo   Aranan konumlar:
echo   - PATH ortam degiskeni
echo   - %COMPOSER_PATH%\composer.bat
echo   - %COMPOSER_PATH%\composer.phar
echo   - %~dp0composer.phar
echo.
echo Composer'i indirmek ister misiniz? (Y/N)
set /p DOWNLOAD_COMPOSER="> "
if /i "%DOWNLOAD_COMPOSER%"=="Y" (
    echo Composer indiriliyor...
    powershell -Command "Invoke-WebRequest -Uri https://getcomposer.org/installer -OutFile composer-setup.php"
    %PHP_PATH%\php.exe composer-setup.php --install-dir=%~dp0 --filename=composer.phar
    del composer-setup.php
    set "COMPOSER_CMD=%PHP_PATH%\php.exe %~dp0composer.phar"
    set "COMPOSER_CALL_PREFIX="
    echo ✓ Composer indirildi
    %COMPOSER_CMD% --version
    goto :composer_found
) else (
    echo.
    echo X Composer gerekli!
    echo.
    echo Composer kurulumu icin:
    echo 1. https://getcomposer.org/download/ adresinden Composer-Setup.exe indirin
    echo 2. Kurulum sirasinda PHP yolunu secin: %PHP_PATH%\php.exe
    echo 3. Kurulum tamamlandiktan sonra bu script'i tekrar calistirin
    echo.
    echo veya
    echo.
    echo 4. Composer.phar dosyasini manuel olarak %COMPOSER_PATH% dizinine koyun
    echo.
    pause
    exit /b 1
)

:composer_found
echo.

echo ======================================
echo 4/8 - Composer Bagimliliklari Yukleniyor
echo ======================================
echo.

if not exist "vendor" (
    echo Bagimliliklar ilk kez yukleniyor...
    echo Bu islem 2-10 dakika surebilir, lutfen bekleyin...
    echo.
    %COMPOSER_CMD% install --no-interaction --prefer-dist --optimize-autoloader --no-ansi
) else (
    echo Vendor klasoru mevcut, bagimliliklari kontrol ediliyor...
    echo.
    %COMPOSER_CMD% install --no-interaction --prefer-dist --optimize-autoloader --no-ansi
)

if errorlevel 1 (
    echo.
    echo X Composer bagimliliklari yuklenemedi!
    echo.
    echo Olasi cozumler:
    echo 1. Internet baglantinizi kontrol edin
    echo 2. composer.lock dosyasini silin ve tekrar deneyin
    echo 3. Manual olarak 'composer install' komutunu calistirin
    echo 4. 'composer clear-cache' komutunu deneyin
    echo.
    pause
    exit /b 1
)
echo.
echo ✓ Bagimliliklar yuklendi
echo.

echo ======================================
echo 5/8 - .env Dosyasi Olusturuluyor
echo ======================================

if not exist ".env" (
    copy .env.example .env >nul
    echo ✓ .env dosyasi olusturuldu
    
    REM .env dosyasını XAMPP MySQL ayarları ile güncelle
    powershell -Command "(Get-Content .env) -replace 'DB_HOST=127.0.0.1', 'DB_HOST=localhost' | Set-Content .env" 2>nul
    powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=simple_lic_manager', 'DB_DATABASE=simple_lic_manager' | Set-Content .env" 2>nul
    powershell -Command "(Get-Content .env) -replace 'DB_USERNAME=root', 'DB_USERNAME=root' | Set-Content .env" 2>nul
    powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=', 'DB_PASSWORD=' | Set-Content .env" 2>nul
    echo ! .env dosyasinda veritabani bilgilerinizi kontrol edin
) else (
    echo i .env dosyasi zaten mevcut
)
echo.

echo ======================================
echo 6/8 - Uygulama Anahtari Olusturuluyor
echo ======================================

call php artisan key:generate --ansi
if errorlevel 1 (
    echo X Anahtar olusturulamadi!
    pause
    exit /b 1
)
echo ✓ Uygulama anahtari olusturuldu
echo.

echo ======================================
echo 7/8 - Storage ve Cache Dizinleri Hazirlaniyor
echo ======================================

REM Storage dizinlerini oluştur
if not exist "storage\app\public" mkdir storage\app\public
if not exist "storage\framework\cache\data" mkdir storage\framework\cache\data
if not exist "storage\framework\sessions" mkdir storage\framework\sessions
if not exist "storage\framework\testing" mkdir storage\framework\testing
if not exist "storage\framework\views" mkdir storage\framework\views
if not exist "storage\logs" mkdir storage\logs
if not exist "bootstrap\cache" mkdir bootstrap\cache

echo ✓ Storage dizinleri olusturuldu

REM Storage dizinlerine yazma izni ver (Windows için)
echo ! Storage klasorlerine tam izin veriliyor...
icacls storage /grant Everyone:F /T >nul 2>&1
icacls bootstrap\cache /grant Everyone:F /T >nul 2>&1
echo ✓ Dizin izinleri ayarlandi
echo.

echo ======================================
echo 8/8 - MySQL Veritabani Kontrolu
echo ======================================

REM XAMPP MySQL'in çalışıp çalışmadığını kontrol et
netstat -ano | findstr ":3306" >nul 2>&1
if errorlevel 1 (
    echo ! MySQL calisiyor gibi gorunmuyor
    echo.
    echo XAMPP Control Panel'den MySQL'i baslatin:
    echo 1. XAMPP Control Panel'i acin (xampp-control.exe)
    echo 2. MySQL'in yanindaki "Start" butonuna tiklayin
    echo 3. MySQL'in yesil "Running" durumuna gelmesini bekleyin
    echo.
    echo MySQL baslatildiktan sonra devam etmek icin bir tusa basin...
    pause
)

REM MySQL bağlantısını test et
echo MySQL baglantisi test ediliyor...
php -r "try { new PDO('mysql:host=localhost', 'root', ''); echo '✓ MySQL baglantisi basarili' . PHP_EOL; } catch(Exception $e) { echo 'X MySQL baglantisi basarisiz: ' . $e->getMessage() . PHP_EOL; exit(1); }"
if errorlevel 1 (
    echo.
    echo XAMPP MySQL sunucusunun calistigindan emin olun!
    pause
    exit /b 1
)
echo.

REM Veritabanı oluştur
echo Veritabani olusturuluyor...
"%MYSQL_PATH%\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS simple_lic_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
if errorlevel 1 (
    echo ! Veritabani otomatik olusturulamadi
    echo.
    echo Manuel olarak olusturmak icin:
    echo 1. phpMyAdmin'i acin (http://localhost/phpmyadmin)
    echo 2. Yeni bir veritabani olusturun: simple_lic_manager
    echo 3. Karakter seti: utf8mb4_unicode_ci
    echo.
    echo Veritabanini olusturduktan sonra devam etmek icin bir tusa basin...
    pause
) else (
    echo ✓ Veritabani olusturuldu: simple_lic_manager
)
echo.

echo Veritabani migration'lari calistiriliyor...
call php artisan migrate --force
if errorlevel 1 (
    echo X Migration basarisiz oldu!
    echo .env dosyasindaki veritabani ayarlarini kontrol edin
    echo.
    pause
    exit /b 1
)
echo ✓ Migration'lar tamamlandi
echo.

echo Ornek veriler yukleniyor (seed)...
call php artisan db:seed --force
if errorlevel 1 (
    echo ! Seed verileri yuklenemedi (bu normal olabilir)
    echo Eger admin kullanicisi gerekiyorsa manuel olarak olusturun
) else (
    echo ✓ Seed verileri yuklendi
)
echo.

echo ======================================
echo ✓✓✓ KURULUM TAMAMLANDI! ✓✓✓
echo ======================================
echo.
echo SimpleLicManager basariyla kuruldu!
echo.
echo ======================================
echo SONRAKI ADIMLAR:
echo ======================================
echo.
echo 1. XAMPP Control Panel'den Apache ve MySQL'in calistigindan emin olun
echo.
echo 2. Gelistirme sunucusunu baslatin:
echo    php artisan serve
echo.
echo 3. Uygulamaya tarayicinizdan erisim saglayin:
echo    http://localhost:8000
echo.
echo 4. Varsayilan giris bilgileri:
echo    Admin: admin@example.com / password
echo    Demo:  demo@example.com / password
echo.
echo ======================================
echo XAMPP VHOST KURULUMU (OPSIYONEL):
echo ======================================
echo.
echo Projeyi http://simplelicmanager.test gibi bir domain ile kullanmak icin:
echo.
echo 1. httpd-vhost.conf dosyasini duzenleyin:
echo    %XAMPP_PATH%\apache\conf\extra\httpd-vhosts.conf
echo.
echo 2. Asagidaki blogu ekleyin:
echo.
echo    ^<VirtualHost *:80^>
echo        DocumentRoot "%~dp0public"
echo        ServerName simplelicmanager.test
echo        ^<Directory "%~dp0public"^>
echo            Options Indexes FollowSymLinks
echo            AllowOverride All
echo            Require all granted
echo        ^</Directory^>
echo    ^</VirtualHost^>
echo.
echo 3. Windows hosts dosyasini duzenleyin:
echo    C:\Windows\System32\drivers\etc\hosts
echo.
echo    127.0.0.1 simplelicmanager.test
echo.
echo 4. Apache'yi yeniden baslatin
echo.
echo ======================================
echo.
echo Keyifli kodlamalar!
echo.
pause