unit MainForm;

{
  SimpleLicManager API - VCL Form Örneği
  
  Bu form, uLicApis ünitesinin VCL uygulamasında nasıl 
  kullanılacağını gösterir.
}

interface

uses
  Winapi.Windows, Winapi.Messages, System.SysUtils, System.Variants, 
  System.Classes, Vcl.Graphics, Vcl.Controls, Vcl.Forms, Vcl.Dialogs, 
  Vcl.StdCtrls, Vcl.ExtCtrls, uLicApis;

type
  TfrmMain = class(TForm)
    pnlTop: TPanel;
    lblTitle: TLabel;
    grpInputs: TGroupBox;
    lblBaseURL: TLabel;
    edtBaseURL: TEdit;
    lblEmail: TLabel;
    edtEmail: TEdit;
    lblSerialNumber: TLabel;
    edtSerialNumber: TEdit;
    lblDeviceID: TLabel;
    edtDeviceID: TEdit;
    btnCheck: TButton;
    grpResult: TGroupBox;
    memoResult: TMemo;
    btnClear: TButton;
    chkUseDeviceID: TCheckBox;
    procedure btnCheckClick(Sender: TObject);
    procedure btnClearClick(Sender: TObject);
    procedure FormCreate(Sender: TObject);
    procedure chkUseDeviceIDClick(Sender: TObject);
  private
    FApi: TLicApis;
    procedure ShowLicenseResult(const AResponse: TLicenseCheckResponse);
    function GetDeviceFingerprint: string;
  public
    destructor Destroy; override;
  end;

var
  frmMain: TfrmMain;

implementation

{$R *.dfm}

procedure TfrmMain.FormCreate(Sender: TObject);
begin
  // API nesnesini oluştur
  FApi := TLicApis.Create;
  
  // Varsayılan değerler
  edtBaseURL.Text := 'http://localhost/api/v1';
  edtEmail.Text := 'demo@example.com';
  edtSerialNumber.Text := 'DEMO-1234-5678-ABCD';
  edtDeviceID.Text := GetDeviceFingerprint;
  edtDeviceID.Enabled := False;
  chkUseDeviceID.Checked := False;
end;

destructor TfrmMain.Destroy;
begin
  FApi.Free;
  inherited;
end;

procedure TfrmMain.chkUseDeviceIDClick(Sender: TObject);
begin
  edtDeviceID.Enabled := chkUseDeviceID.Checked;
end;

procedure TfrmMain.btnCheckClick(Sender: TObject);
var
  Response: TLicenseCheckResponse;
  DeviceID: string;
begin
  // Validasyon
  if Trim(edtBaseURL.Text) = '' then
  begin
    ShowMessage('Lütfen Base URL girin!');
    edtBaseURL.SetFocus;
    Exit;
  end;
  
  if Trim(edtEmail.Text) = '' then
  begin
    ShowMessage('Lütfen Email girin!');
    edtEmail.SetFocus;
    Exit;
  end;
  
  if Trim(edtSerialNumber.Text) = '' then
  begin
    ShowMessage('Lütfen Seri Numarası girin!');
    edtSerialNumber.SetFocus;
    Exit;
  end;
  
  // Kontrol yap
  Screen.Cursor := crHourGlass;
  btnCheck.Enabled := False;
  try
    memoResult.Lines.Clear;
    memoResult.Lines.Add('API isteği gönderiliyor...');
    Application.ProcessMessages;
    
    // Base URL'i ayarla
    FApi.BaseURL := edtBaseURL.Text;
    
    // Cihaz ID'sini belirle
    if chkUseDeviceID.Checked then
      DeviceID := edtDeviceID.Text
    else
      DeviceID := '';
    
    // API çağrısı yap
    Response := FApi.CheckLicense(
      edtEmail.Text,
      edtSerialNumber.Text,
      DeviceID
    );
    
    // Sonucu göster
    ShowLicenseResult(Response);
    
  finally
    Screen.Cursor := crDefault;
    btnCheck.Enabled := True;
  end;
end;

procedure TfrmMain.ShowLicenseResult(const AResponse: TLicenseCheckResponse);
begin
  memoResult.Lines.Clear;
  memoResult.Lines.Add('=== SONUÇ ===');
  memoResult.Lines.Add('');
  
  // HTTP durum kodu
  memoResult.Lines.Add('HTTP Durum Kodu: ' + IntToStr(AResponse.HTTPStatusCode));
  memoResult.Lines.Add('');
  
  // Geçerlilik durumu
  if AResponse.Valid then
  begin
    memoResult.Lines.Add('✓ LİSANS GEÇERLİ');
    memoResult.Lines.Add('');
    memoResult.Lines.Add('Paket: ' + AResponse.Package);
    memoResult.Lines.Add('Lisans Tipi: ' + AResponse.LicenseType);
    memoResult.Lines.Add('Acil Durum: ' + BoolToStr(AResponse.Emergency, True));
    
    if AResponse.IsLifetime then
    begin
      memoResult.Lines.Add('Süre: Ömür Boyu');
    end
    else
    begin
      memoResult.Lines.Add('Bitiş Tarihi: ' + AResponse.ExpiresAt);
      memoResult.Lines.Add('Kalan Gün: ' + IntToStr(AResponse.DaysLeft));
    end;
    
    if AResponse.Warning <> '' then
    begin
      memoResult.Lines.Add('');
      memoResult.Lines.Add('⚠ UYARI: ' + AResponse.Warning);
    end;
  end
  else
  begin
    memoResult.Lines.Add('✗ LİSANS GEÇERSİZ');
    memoResult.Lines.Add('');
    
    if AResponse.ErrorMessage <> '' then
      memoResult.Lines.Add('Hata Mesajı: ' + AResponse.ErrorMessage);
    
    // HTTP koduna göre ek bilgi
    case AResponse.HTTPStatusCode of
      403: memoResult.Lines.Add('Erişim reddedildi (Kullanıcı devre dışı veya cihaz eşleşmedi)');
      404: memoResult.Lines.Add('Lisans bulunamadı');
      422: memoResult.Lines.Add('Validation hatası (Geçersiz parametreler)');
      429: memoResult.Lines.Add('Rate limit aşıldı (Çok fazla istek)');
      0: memoResult.Lines.Add('Bağlantı hatası (Sunucuya ulaşılamadı)');
    end;
  end;
  
  memoResult.Lines.Add('');
  memoResult.Lines.Add('=============');
end;

procedure TfrmMain.btnClearClick(Sender: TObject);
begin
  memoResult.Lines.Clear;
end;

function TfrmMain.GetDeviceFingerprint: string;
var
  ComputerName: array[0..MAX_COMPUTERNAME_LENGTH] of Char;
  Size: DWORD;
begin
  Size := MAX_COMPUTERNAME_LENGTH + 1;
  if GetComputerName(@ComputerName, Size) then
    Result := 'DEVICE-' + ComputerName
  else
    Result := 'DEVICE-UNKNOWN';
end;

end.
