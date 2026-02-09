unit uLicApis;

{
  SimpleLicManager API İstemci Kütüphanesi
  
  Bu ünite, SimpleLicManager API'leri ile etkileşim kurmak için gerekli 
  fonksiyonları ve veri yapılarını sağlar.
  
  Kullanım:
  - Class fonksiyon olarak: TLicApis.CheckLicense(...)
  - Instance olarak: 
      var Api: TLicApis;
      Api := TLicApis.Create('http://yourdomain.com');
      Api.CheckLicense(...);
}

interface

uses
  System.SysUtils, System.Classes, System.JSON, System.Net.HttpClient,
  System.Net.URLClient;

type
  // Lisans kontrol yanıt kaydı
  TLicenseCheckResponse = record
    Valid: Boolean;              // Lisansın geçerli olup olmadığı
    Package: string;             // Ürün paketi adı
    LicenseType: string;         // Lisans tipi (demo, monthly, yearly, lifetime)
    Emergency: Boolean;          // Acil durum etiketi
    ExpiresAt: string;           // Bitiş tarihi (YYYY-MM-DD), lifetime için boş
    DaysLeft: Integer;           // Kalan gün sayısı, lifetime için -1
    Warning: string;             // Opsiyonel uyarı mesajı
    ErrorMessage: string;        // Hata durumunda hata mesajı
    HTTPStatusCode: Integer;     // HTTP durum kodu
    
    // Yardımcı metodlar
    function IsLifetime: Boolean;
    function HasError: Boolean;
    function ToString: string;
  end;

  // API istemci sınıfı
  TLicApis = class
  private
    FBaseURL: string;
    FTimeout: Integer;
    
    class function ParseLicenseCheckResponse(const AJSONContent: string; 
      AStatusCode: Integer): TLicenseCheckResponse; static;
    class function CreateJSONRequest(const AEmail, ASerialNumber, 
      ADeviceID: string): TJSONObject; static;
  public
    constructor Create(const ABaseURL: string = 'http://localhost/api/v1');
    
    // Instance metod
    function CheckLicense(const AEmail, ASerialNumber: string; 
      const ADeviceID: string = ''): TLicenseCheckResponse;
    
    // Class fonksiyon - statik kullanım için
    class function CheckLicenseStatic(const ABaseURL, AEmail, ASerialNumber: string; 
      const ADeviceID: string = ''): TLicenseCheckResponse; static;
    
    property BaseURL: string read FBaseURL write FBaseURL;
    property Timeout: Integer read FTimeout write FTimeout; // Milisaniye cinsinden
  end;

implementation

{ TLicenseCheckResponse }

function TLicenseCheckResponse.IsLifetime: Boolean;
begin
  Result := (LicenseType = 'lifetime') or (DaysLeft = -1);
end;

function TLicenseCheckResponse.HasError: Boolean;
begin
  Result := not Valid or (ErrorMessage <> '');
end;

function TLicenseCheckResponse.ToString: string;
begin
  if HasError then
    Result := Format('Hata: %s (HTTP: %d)', [ErrorMessage, HTTPStatusCode])
  else
    Result := Format('Geçerli: %s, Paket: %s, Tip: %s, Kalan Gün: %d', 
      [BoolToStr(Valid, True), Package, LicenseType, DaysLeft]);
end;

{ TLicApis }

constructor TLicApis.Create(const ABaseURL: string);
begin
  inherited Create;
  FBaseURL := ABaseURL;
  FTimeout := 30000; // 30 saniye varsayılan timeout
end;

class function TLicApis.CreateJSONRequest(const AEmail, ASerialNumber, 
  ADeviceID: string): TJSONObject;
begin
  Result := TJSONObject.Create;
  try
    Result.AddPair('email', AEmail);
    Result.AddPair('serial_number', ASerialNumber);
    
    // Device ID opsiyonel
    if ADeviceID <> '' then
      Result.AddPair('device_id', ADeviceID);
  except
    Result.Free;
    raise;
  end;
end;

class function TLicApis.ParseLicenseCheckResponse(const AJSONContent: string; 
  AStatusCode: Integer): TLicenseCheckResponse;
var
  JSONObj: TJSONObject;
  JSONValue: TJSONValue;
begin
  // Varsayılan değerler
  Result.Valid := False;
  Result.Package := '';
  Result.LicenseType := '';
  Result.Emergency := False;
  Result.ExpiresAt := '';
  Result.DaysLeft := -1;
  Result.Warning := '';
  Result.ErrorMessage := '';
  Result.HTTPStatusCode := AStatusCode;
  
  try
    JSONObj := TJSONObject.ParseJSONValue(AJSONContent) as TJSONObject;
    if JSONObj = nil then
    begin
      Result.ErrorMessage := 'JSON parse hatası';
      Exit;
    end;
    
    try
      // Valid alanı (zorunlu)
      JSONValue := JSONObj.GetValue('valid');
      if JSONValue <> nil then
        Result.Valid := JSONValue.GetValue<Boolean>;
      
      // Hata durumunda message alanını oku
      if not Result.Valid then
      begin
        JSONValue := JSONObj.GetValue('message');
        if JSONValue <> nil then
          Result.ErrorMessage := JSONValue.Value;
        Exit;
      end;
      
      // Başarılı yanıt alanları
      JSONValue := JSONObj.GetValue('package');
      if JSONValue <> nil then
        Result.Package := JSONValue.Value;
      
      JSONValue := JSONObj.GetValue('type');
      if JSONValue <> nil then
        Result.LicenseType := JSONValue.Value;
      
      JSONValue := JSONObj.GetValue('emergency');
      if JSONValue <> nil then
        Result.Emergency := JSONValue.GetValue<Boolean>;
      
      JSONValue := JSONObj.GetValue('expires_at');
      if (JSONValue <> nil) and not (JSONValue is TJSONNull) then
        Result.ExpiresAt := JSONValue.Value;
      
      JSONValue := JSONObj.GetValue('days_left');
      if (JSONValue <> nil) and not (JSONValue is TJSONNull) then
        Result.DaysLeft := JSONValue.GetValue<Integer>;
      
      JSONValue := JSONObj.GetValue('warning');
      if (JSONValue <> nil) and not (JSONValue is TJSONNull) then
        Result.Warning := JSONValue.Value;
        
    finally
      JSONObj.Free;
    end;
  except
    on E: Exception do
    begin
      Result.ErrorMessage := 'JSON işleme hatası: ' + E.Message;
    end;
  end;
end;

function TLicApis.CheckLicense(const AEmail, ASerialNumber: string; 
  const ADeviceID: string = ''): TLicenseCheckResponse;
begin
  Result := CheckLicenseStatic(FBaseURL, AEmail, ASerialNumber, ADeviceID);
end;

class function TLicApis.CheckLicenseStatic(const ABaseURL, AEmail, 
  ASerialNumber: string; const ADeviceID: string = ''): TLicenseCheckResponse;
var
  HTTPClient: THTTPClient;
  JSONRequest: TJSONObject;
  RequestBody: TStringStream;
  Response: IHTTPResponse;
  URL: string;
begin
  // Varsayılan hata yanıtı
  Result.Valid := False;
  Result.ErrorMessage := '';
  Result.HTTPStatusCode := 0;
  
  HTTPClient := THTTPClient.Create;
  try
    // Timeout ayarı
    HTTPClient.ConnectionTimeout := 30000;
    HTTPClient.ResponseTimeout := 30000;
    
    // Request JSON hazırla
    JSONRequest := CreateJSONRequest(AEmail, ASerialNumber, ADeviceID);
    try
      RequestBody := TStringStream.Create(JSONRequest.ToString, TEncoding.UTF8);
      try
        // URL oluştur
        URL := ABaseURL;
        if not URL.EndsWith('/') then
          URL := URL + '/';
        URL := URL + 'license/check';
        
        // HTTP Headers
        HTTPClient.Accept := 'application/json';
        HTTPClient.ContentType := 'application/json';
        
        try
          // POST isteği gönder
          Response := HTTPClient.Post(URL, RequestBody);
          
          // Yanıtı parse et
          Result := ParseLicenseCheckResponse(Response.ContentAsString, 
            Response.StatusCode);
            
        except
          on E: Exception do
          begin
            Result.ErrorMessage := 'HTTP hatası: ' + E.Message;
            Result.HTTPStatusCode := 0;
          end;
        end;
        
      finally
        RequestBody.Free;
      end;
    finally
      JSONRequest.Free;
    end;
  finally
    HTTPClient.Free;
  end;
end;

end.
