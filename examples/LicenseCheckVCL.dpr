program LicenseCheckVCL;

uses
  Vcl.Forms,
  MainForm in 'MainForm.pas' {frmMain},
  uLicApis in 'uLicApis.pas';

{$R *.res}

begin
  Application.Initialize;
  Application.MainFormOnTaskbar := True;
  Application.Title := 'SimpleLicManager API - VCL';
  Application.CreateForm(TfrmMain, frmMain);
  Application.Run;
end.
