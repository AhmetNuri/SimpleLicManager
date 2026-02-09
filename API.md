# SimpleLicManager API Dokümantasyonu

## Base URL
```
http://yourdomain.com/api/v1
```

## Authentication
API endpoint'leri authentication gerektirmez. Ancak rate limiting aktiftir (60 req/min).

---

## Endpoints

### 1. Lisans Kontrolü

Bir lisansın geçerliliğini kontrol eder.

**Endpoint:** `POST /license/check`

**Headers:**
```
Content-Type: application/json
Accept: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "serial_number": "XXXX-XXXX-XXXX-XXXX",
  "device_id": "optional-hardware-fingerprint"
}
```

**Parametreler:**

| Parametre | Tip | Zorunlu | Açıklama |
|-----------|-----|---------|----------|
| email | string | Evet | Kullanıcının e-posta adresi |
| serial_number | string | Evet | Lisans seri numarası |
| device_id | string | Hayır | Cihaz donanım parmak izi |

**Başarılı Response (200):**
```json
{
  "valid": true,
  "package": "ModalMasterPro",
  "type": "yearly",
  "emergency": false,
  "expires_at": "2027-02-09",
  "days_left": 364,
  "warning": "Lisansınızın bitmesine 8 günden az kaldı!"
}
```

**Başarılı Response Alanları:**

| Alan | Tip | Açıklama |
|------|-----|----------|
| valid | boolean | Lisansın geçerli olup olmadığı |
| package | string | Ürün paketi adı |
| type | string | Lisans tipi (demo, monthly, yearly, lifetime) |
| emergency | boolean | Acil durum etiketi |
| expires_at | string\|null | Bitiş tarihi (YYYY-MM-DD), lifetime için null |
| days_left | integer\|null | Kalan gün sayısı, lifetime için null |
| warning | string | Opsiyonel, 10 günden az kaldıysa uyarı mesajı |

**Hata Response (404 - Lisans Bulunamadı):**
```json
{
  "valid": false,
  "message": "Lisans bulunamadı."
}
```

**Hata Response (403 - Kullanıcı Devre Dışı):**
```json
{
  "valid": false,
  "message": "Kullanıcı devre dışı bırakılmış."
}
```

**Hata Response (403 - Cihaz Eşleşmedi):**
```json
{
  "valid": false,
  "message": "Cihaz eşleşmedi. Bu lisans başka bir cihaza bağlı."
}
```

**Hata Response (403 - Süresi Dolmuş):**
```json
{
  "valid": false,
  "message": "Lisans süresi dolmuş."
}
```

**Hata Response (422 - Validation Hatası):**
```json
{
  "valid": false,
  "message": "Validation failed: The email field is required."
}
```

**Hata Response (429 - Rate Limit Aşıldı):**
```json
{
  "message": "Too Many Attempts."
}
```

---

## Lisans Tipleri

- **demo**: Demo lisans
- **monthly**: Aylık lisans
- **yearly**: Yıllık lisans
- **lifetime**: Ömür boyu lisans (expires_at = null)

---

## Cihaz Bağlama Mantığı

1. İlk API çağrısında `device_id` gönderilirse:
   - Lisansa cihaz ID'si kaydedilir
   - Sonraki çağrılarda aynı cihaz ID'si kontrol edilir

2. `device_id` gönderilmezse:
   - Sadece email ve serial_number kontrolü yapılır
   - Cihaz bağlama devre dışı kalır

3. Farklı bir cihazdan erişim:
   - Hata döner: "Cihaz eşleşmedi"
   - Admin panelden cihaz ID'si sıfırlanabilir

---

## Rate Limiting

- **Limit:** 60 istek / dakika
- **Aşıldığında:** HTTP 429 hatası döner
- **Reset:** Her dakika başında sıfırlanır

---

## Error Codes

| HTTP Code | Açıklama |
|-----------|----------|
| 200 | Başarılı |
| 403 | Yetkisiz (kullanıcı devre dışı, cihaz eşleşmedi, vb.) |
| 404 | Bulunamadı (lisans yok) |
| 422 | Validation hatası |
| 429 | Rate limit aşıldı |
| 500 | Sunucu hatası |

---

## Örnek Kullanımlar

### cURL

**Temel Lisans Kontrolü:**
```bash
curl -X POST http://yourdomain.com/api/v1/license/check \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "demo@example.com",
    "serial_number": "DEMO-1234-5678-ABCD"
  }'
```

**Cihaz ID ile Lisans Kontrolü:**
```bash
curl -X POST http://yourdomain.com/api/v1/license/check \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "demo@example.com",
    "serial_number": "DEMO-1234-5678-ABCD",
    "device_id": "MY-DEVICE-001"
  }'
```

### JavaScript (Fetch)

```javascript
async function checkLicense(email, serialNumber, deviceId = null) {
  const response = await fetch('http://yourdomain.com/api/v1/license/check', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify({
      email: email,
      serial_number: serialNumber,
      device_id: deviceId
    })
  });
  
  const data = await response.json();
  return data;
}

// Kullanım
checkLicense('demo@example.com', 'DEMO-1234-5678-ABCD')
  .then(result => {
    if (result.valid) {
      console.log('Lisans geçerli:', result);
    } else {
      console.log('Lisans geçersiz:', result.message);
    }
  });
```

### Python (Requests)

```python
import requests

def check_license(email, serial_number, device_id=None):
    url = 'http://yourdomain.com/api/v1/license/check'
    headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
    data = {
        'email': email,
        'serial_number': serial_number
    }
    if device_id:
        data['device_id'] = device_id
    
    response = requests.post(url, json=data, headers=headers)
    return response.json()

# Kullanım
result = check_license('demo@example.com', 'DEMO-1234-5678-ABCD')
if result.get('valid'):
    print('Lisans geçerli:', result)
else:
    print('Lisans geçersiz:', result.get('message'))
```

### Delphi (REST Client)

```pascal
procedure TForm1.CheckLicense;
var
  RESTClient: TRESTClient;
  RESTRequest: TRESTRequest;
  RESTResponse: TRESTResponse;
  JSONObj, ResultObj: TJSONObject;
begin
  RESTClient := TRESTClient.Create('http://yourdomain.com/api/v1');
  RESTRequest := TRESTRequest.Create(nil);
  RESTResponse := TRESTResponse.Create(nil);
  
  try
    RESTRequest.Client := RESTClient;
    RESTRequest.Response := RESTResponse;
    RESTRequest.Resource := 'license/check';
    RESTRequest.Method := rmPOST;
    
    // Request body oluştur
    JSONObj := TJSONObject.Create;
    try
      JSONObj.AddPair('email', 'demo@example.com');
      JSONObj.AddPair('serial_number', 'DEMO-1234-5678-ABCD');
      JSONObj.AddPair('device_id', GetDeviceFingerprint);
      
      RESTRequest.AddBody(JSONObj.ToString, ctAPPLICATION_JSON);
      RESTRequest.Execute;
      
      if RESTResponse.StatusCode = 200 then
      begin
        // Response parse et
        ResultObj := TJSONObject.ParseJSONValue(RESTResponse.Content) as TJSONObject;
        try
          if ResultObj.GetValue<Boolean>('valid') then
          begin
            ShowMessage('Lisans geçerli!' + sLineBreak +
                       'Paket: ' + ResultObj.GetValue<string>('package') + sLineBreak +
                       'Tip: ' + ResultObj.GetValue<string>('type'));
          end
          else
          begin
            ShowMessage('Lisans geçersiz: ' + ResultObj.GetValue<string>('message'));
          end;
        finally
          ResultObj.Free;
        end;
      end
      else
      begin
        ShowMessage('Hata: ' + RESTResponse.StatusText);
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

// Cihaz parmak izi alma fonksiyonu (örnek)
function TForm1.GetDeviceFingerprint: string;
var
  ComputerName: array[0..MAX_COMPUTERNAME_LENGTH] of Char;
  Size: DWORD;
begin
  Size := MAX_COMPUTERNAME_LENGTH + 1;
  if GetComputerName(@ComputerName, Size) then
    Result := ComputerName
  else
    Result := 'UNKNOWN';
end;
```

---

## Best Practices

1. **Cihaz ID Kullanımı:**
   - Benzersiz bir donanım parmak izi kullanın
   - CPU ID, MAC adresi gibi değerler birleştirilebilir
   - Hash'lenmiş değer kullanmak önerilir

2. **Hata Yönetimi:**
   - Tüm HTTP status code'larını handle edin
   - Kullanıcıya anlamlı hata mesajları gösterin
   - Network hatalarını yakalayın

3. **Güvenlik:**
   - HTTPS kullanın (production'da zorunlu)
   - API key'leri güvenli saklayın
   - Rate limiting'e dikkat edin

4. **Önbellekleme:**
   - Lisans kontrolünü her işlemde yapmayın
   - Geçerli lisans bilgisini lokal cache'leyin
   - Düzenli aralıklarla (günlük) yeniden kontrol edin

---

## Loglama

Tüm API istekleri `lic_logs` tablosunda loglanır:
- Başarılı kontroller: `info` seviyesi
- Başarısız kontroller: `error` seviyesi
- Cihaz değişiklikleri: `info` seviyesi

Admin panelden loglar görüntülenebilir.

---

## Destek

API ile ilgili sorularınız için:
- GitHub Issues: https://github.com/AhmetNuri/SimpleLicManager/issues
- Email: support@yourdomain.com
