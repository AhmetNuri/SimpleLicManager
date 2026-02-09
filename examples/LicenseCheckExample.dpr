{
  SimpleLicManager API Kullanım Örnekleri
  
  Bu dosya, uLicApis.pas ünitesinin nasıl kullanılacağını gösterir.
}

program LicenseCheckExample;

{$APPTYPE CONSOLE}

uses
  System.SysUtils,
  uLicApis in 'uLicApis.pas';

procedure Example1_ClassFunctionUsage;
var
  Response: TLicenseCheckResponse;
begin
  WriteLn('=== Örnek 1: Class Fonksiyon Kullanımı ===');
  WriteLn;
  
  // Statik metod ile kullanım - nesne oluşturmaya gerek yok
  Response := TLicApis.CheckLicenseStatic(
    'http://localhost/api/v1',
    'demo@example.com',
    'DEMO-1234-5678-ABCD'
  );
  
  // Sonucu kontrol et
  if Response.Valid then
  begin
    WriteLn('✓ Lisans geçerli!');
    WriteLn('  Paket: ', Response.Package);
    WriteLn('  Tip: ', Response.LicenseType);
    WriteLn('  Acil Durum: ', BoolToStr(Response.Emergency, True));
    
    if Response.IsLifetime then
      WriteLn('  Süre: Ömür boyu')
    else
    begin
      WriteLn('  Bitiş Tarihi: ', Response.ExpiresAt);
      WriteLn('  Kalan Gün: ', Response.DaysLeft);
    end;
    
    if Response.Warning <> '' then
      WriteLn('  ⚠ Uyarı: ', Response.Warning);
  end
  else
  begin
    WriteLn('✗ Lisans geçersiz!');
    WriteLn('  Hata: ', Response.ErrorMessage);
    WriteLn('  HTTP Kodu: ', Response.HTTPStatusCode);
  end;
  
  WriteLn;
end;

procedure Example2_InstanceUsage;
var
  Api: TLicApis;
  Response: TLicenseCheckResponse;
begin
  WriteLn('=== Örnek 2: Instance (Nesne) Kullanımı ===');
  WriteLn;
  
  // API nesnesi oluştur
  Api := TLicApis.Create('http://localhost/api/v1');
  try
    // Timeout ayarla (opsiyonel)
    Api.Timeout := 15000; // 15 saniye
    
    // Lisans kontrolü yap
    Response := Api.CheckLicense(
      'user@example.com',
      'XXXX-YYYY-ZZZZ-WWWW'
    );
    
    // ToString metodunu kullanarak sonucu göster
    WriteLn('Sonuç: ', Response.ToString);
  finally
    Api.Free;
  end;
  
  WriteLn;
end;

procedure Example3_WithDeviceID;
var
  Response: TLicenseCheckResponse;
  DeviceID: string;
begin
  WriteLn('=== Örnek 3: Cihaz ID ile Kullanım ===');
  WriteLn;
  
  // Basit bir cihaz ID oluştur (gerçek uygulamada daha güvenli olmalı)
  DeviceID := 'DEVICE-' + GetComputerName;
  
  WriteLn('Kullanılan Cihaz ID: ', DeviceID);
  WriteLn;
  
  Response := TLicApis.CheckLicenseStatic(
    'http://localhost/api/v1',
    'demo@example.com',
    'DEMO-1234-5678-ABCD',
    DeviceID  // Cihaz ID parametresi
  );
  
  if Response.HasError then
    WriteLn('Hata: ', Response.ErrorMessage)
  else
    WriteLn('Başarılı: Lisans geçerli');
    
  WriteLn;
end;

procedure Example4_ErrorHandling;
var
  Response: TLicenseCheckResponse;
begin
  WriteLn('=== Örnek 4: Hata Yönetimi ===');
  WriteLn;
  
  // Geçersiz endpoint ile test
  Response := TLicApis.CheckLicenseStatic(
    'http://invalid-url.local/api/v1',
    'test@example.com',
    'TEST-0000-0000-0000'
  );
  
  // Hata kontrolü
  if Response.HasError then
  begin
    WriteLn('Beklenen hata oluştu:');
    WriteLn('  Mesaj: ', Response.ErrorMessage);
    WriteLn('  HTTP Kodu: ', Response.HTTPStatusCode);
    
    // HTTP koduna göre işlem
    case Response.HTTPStatusCode of
      403: WriteLn('  → Erişim reddedildi');
      404: WriteLn('  → Lisans bulunamadı');
      422: WriteLn('  → Validation hatası');
      429: WriteLn('  → Rate limit aşıldı');
      0: WriteLn('  → Bağlantı hatası');
    end;
  end;
  
  WriteLn;
end;

procedure Example5_MultipleChecks;
var
  Api: TLicApis;
  Licenses: array[0..2] of record
    Email: string;
    Serial: string;
  end;
  I: Integer;
  Response: TLicenseCheckResponse;
begin
  WriteLn('=== Örnek 5: Çoklu Lisans Kontrolü ===');
  WriteLn;
  
  // Test verileri
  Licenses[0].Email := 'user1@example.com';
  Licenses[0].Serial := 'XXXX-1111-1111-1111';
  
  Licenses[1].Email := 'user2@example.com';
  Licenses[1].Serial := 'XXXX-2222-2222-2222';
  
  Licenses[2].Email := 'user3@example.com';
  Licenses[2].Serial := 'XXXX-3333-3333-3333';
  
  // API nesnesi oluştur (tekrar kullanılabilir)
  Api := TLicApis.Create('http://localhost/api/v1');
  try
    for I := Low(Licenses) to High(Licenses) do
    begin
      WriteLn(Format('Kontrol %d:', [I + 1]));
      WriteLn('  Email: ', Licenses[I].Email);
      WriteLn('  Serial: ', Licenses[I].Serial);
      
      Response := Api.CheckLicense(
        Licenses[I].Email,
        Licenses[I].Serial
      );
      
      if Response.Valid then
        WriteLn('  Durum: ✓ Geçerli')
      else
        WriteLn('  Durum: ✗ Geçersiz - ', Response.ErrorMessage);
        
      WriteLn;
    end;
  finally
    Api.Free;
  end;
end;

function GetComputerName: string;
{$IFDEF MSWINDOWS}
var
  Buffer: array[0..MAX_COMPUTERNAME_LENGTH] of Char;
  Size: DWORD;
begin
  Size := MAX_COMPUTERNAME_LENGTH + 1;
  if Windows.GetComputerName(@Buffer, Size) then
    Result := Buffer
  else
    Result := 'UNKNOWN';
end;
{$ELSE}
begin
  Result := 'UNIX-DEVICE';
end;
{$ENDIF}

begin
  try
    WriteLn('SimpleLicManager API - Kullanım Örnekleri');
    WriteLn('==========================================');
    WriteLn;
    
    // Tüm örnekleri çalıştır
    Example1_ClassFunctionUsage;
    Example2_InstanceUsage;
    Example3_WithDeviceID;
    Example4_ErrorHandling;
    Example5_MultipleChecks;
    
    WriteLn('==========================================');
    WriteLn('Örnekler tamamlandı.');
    WriteLn;
    WriteLn('Devam etmek için ENTER tuşuna basın...');
    ReadLn;
    
  except
    on E: Exception do
    begin
      WriteLn('Hata: ', E.ClassName, ': ', E.Message);
      ReadLn;
    end;
  end;
end.
