# SimpleLicManager - Delphi API İstemci Kütüphanesi

Bu klasör, SimpleLicManager API'lerini Delphi uygulamalarından kullanmak için gerekli dosyaları içerir.

## Dosyalar

### uLicApis.pas
Ana API istemci kütüphanesi. Tüm API metodlarını içerir.

**Özellikler:**
- JSON yanıtları otomatik parse eder
- Record yapıları ile tip-güvenli veri döndürür
- Hem class function (statik) hem de instance kullanımı destekler
- Hata yönetimi ve HTTP durum kodu kontrolü
- Timeout ayarlanabilir

### LicenseCheckExample.dpr
Konsol uygulaması örneği. `uLicApis.pas` ünitesinin kullanımını gösterir.

**İçerdiği örnekler:**
1. Class function kullanımı (statik metod)
2. Instance (nesne) kullanımı
3. Cihaz ID ile lisans kontrolü
4. Hata yönetimi
5. Çoklu lisans kontrolü

## Kurulum

### 1. Projenize Ekleme

Delphi projenize `uLicApis.pas` dosyasını ekleyin:

```
Project → Add to Project → uLicApis.pas
```

### 2. Uses Bölümüne Ekleme

```pascal
uses
  uLicApis;
```

## Kullanım

### Yöntem 1: Class Function (Statik Kullanım)

Nesne oluşturmadan direkt kullanım:

```pascal
var
  Response: TLicenseCheckResponse;
begin
  Response := TLicApis.CheckLicenseStatic(
    'http://yourdomain.com/api/v1',  // Base URL
    'user@example.com',               // Email
    'XXXX-XXXX-XXXX-XXXX'            // Serial Number
  );
  
  if Response.Valid then
    ShowMessage('Lisans geçerli!')
  else
    ShowMessage('Hata: ' + Response.ErrorMessage);
end;
```

### Yöntem 2: Instance (Nesne) Kullanımı

Nesne oluşturarak kullanım (çoklu çağrılar için daha verimli):

```pascal
var
  Api: TLicApis;
  Response: TLicenseCheckResponse;
begin
  Api := TLicApis.Create('http://yourdomain.com/api/v1');
  try
    // Opsiyonel: Timeout ayarla (milisaniye)
    Api.Timeout := 30000;
    
    Response := Api.CheckLicense(
      'user@example.com',
      'XXXX-XXXX-XXXX-XXXX'
    );
    
    if Response.Valid then
      ShowMessage('Lisans geçerli!');
  finally
    Api.Free;
  end;
end;
```

### Yöntem 3: Cihaz ID ile Kullanım

```pascal
var
  Response: TLicenseCheckResponse;
  DeviceID: string;
begin
  DeviceID := GetMyDeviceFingerprint; // Kendi cihaz ID fonksiyonunuz
  
  Response := TLicApis.CheckLicenseStatic(
    'http://yourdomain.com/api/v1',
    'user@example.com',
    'XXXX-XXXX-XXXX-XXXX',
    DeviceID  // Opsiyonel cihaz ID parametresi
  );
end;
```

## TLicenseCheckResponse Kaydı

API yanıtı aşağıdaki alanlara sahip bir record ile döner:

| Alan | Tip | Açıklama |
|------|-----|----------|
| `Valid` | Boolean | Lisansın geçerli olup olmadığı |
| `Package` | string | Ürün paketi adı |
| `LicenseType` | string | Lisans tipi (demo, monthly, yearly, lifetime) |
| `Emergency` | Boolean | Acil durum etiketi |
| `ExpiresAt` | string | Bitiş tarihi (YYYY-MM-DD), lifetime için boş |
| `DaysLeft` | Integer | Kalan gün sayısı, lifetime için -1 |
| `Warning` | string | Opsiyonel uyarı mesajı |
| `ErrorMessage` | string | Hata durumunda hata mesajı |
| `HTTPStatusCode` | Integer | HTTP durum kodu |

### Yardımcı Metodlar

```pascal
// Ömür boyu lisans kontrolü
if Response.IsLifetime then
  ShowMessage('Bu ömür boyu lisans!');

// Hata kontrolü
if Response.HasError then
  ShowMessage('Hata oluştu: ' + Response.ErrorMessage);

// String olarak göster
ShowMessage(Response.ToString);
```

## Hata Yönetimi

API çağrıları sırasında oluşabilecek hatalar:

| HTTP Kodu | Açıklama |
|-----------|----------|
| 200 | Başarılı |
| 403 | Yetkisiz (kullanıcı devre dışı, cihaz eşleşmedi) |
| 404 | Lisans bulunamadı |
| 422 | Validation hatası |
| 429 | Rate limit aşıldı (60 req/dk) |
| 0 | Bağlantı hatası (sunucuya ulaşılamadı) |

### Örnek Hata Kontrolü

```pascal
case Response.HTTPStatusCode of
  200:
    ShowMessage('Başarılı');
  403:
    ShowMessage('Erişim reddedildi: ' + Response.ErrorMessage);
  404:
    ShowMessage('Lisans bulunamadı');
  422:
    ShowMessage('Geçersiz veri: ' + Response.ErrorMessage);
  429:
    ShowMessage('Çok fazla istek gönderildi, lütfen bekleyin');
  0:
    ShowMessage('Sunucuya bağlanılamadı: ' + Response.ErrorMessage);
end;
```

## Örnek Uygulamayı Çalıştırma

1. Delphi IDE'yi açın
2. `LicenseCheckExample.dpr` dosyasını açın
3. F9 ile çalıştırın

**Not:** Örnekleri çalıştırmadan önce:
- SimpleLicManager sunucunuzun çalıştığından emin olun
- `http://localhost/api/v1` URL'ini kendi sunucu adresinizle değiştirin
- Geçerli test email ve serial number kullanın

## Gereksinimler

- Delphi 10 Seattle veya üzeri (System.JSON ve System.Net.HttpClient için)
- İnternet bağlantısı (API çağrıları için)

## API Referansı

API hakkında detaylı bilgi için projenin ana klasöründeki `API.md` dosyasına bakın.

## Lisans

Bu kütüphane SimpleLicManager projesinin bir parçasıdır.

## Destek

Sorularınız için:
- GitHub Issues: https://github.com/AhmetNuri/SimpleLicManager/issues
- API Dokümantasyonu: `/API.md`

## Değişiklik Geçmişi

### v1.0.0 (2026-02-09)
- İlk sürüm
- `/license/check` endpoint desteği
- Class function ve instance kullanımı
- JSON parse ve record dönüşümü
- Hata yönetimi
- Örnek uygulamalar
