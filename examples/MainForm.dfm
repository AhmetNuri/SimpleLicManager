object frmMain: TfrmMain
  Left = 0
  Top = 0
  Caption = 'SimpleLicManager API - VCL '#214'rne'#287'i'
  ClientHeight = 550
  ClientWidth = 650
  Color = clBtnFace
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -12
  Font.Name = 'Segoe UI'
  Font.Style = []
  Position = poScreenCenter
  OnCreate = FormCreate
  TextHeight = 15
  object pnlTop: TPanel
    Left = 0
    Top = 0
    Width = 650
    Height = 50
    Align = alTop
    BevelOuter = bvNone
    Color = 5395026
    ParentBackground = False
    TabOrder = 0
    object lblTitle: TLabel
      Left = 16
      Top = 14
      Width = 258
      Height = 21
      Caption = 'SimpleLicManager API Lisans Kontrol'
      Font.Charset = DEFAULT_CHARSET
      Font.Color = clWhite
      Font.Height = -16
      Font.Name = 'Segoe UI'
      Font.Style = [fsBold]
      ParentFont = False
    end
  end
  object grpInputs: TGroupBox
    Left = 8
    Top = 56
    Width = 634
    Height = 217
    Caption = ' Giri'#351' Parametreleri '
    TabOrder = 1
    object lblBaseURL: TLabel
      Left = 16
      Top = 24
      Width = 53
      Height = 15
      Caption = 'Base URL:'
    end
    object lblEmail: TLabel
      Left = 16
      Top = 72
      Width = 37
      Height = 15
      Caption = 'Email:'
    end
    object lblSerialNumber: TLabel
      Left = 16
      Top = 120
      Width = 90
      Height = 15
      Caption = 'Seri Numaras'#305':'
    end
    object lblDeviceID: TLabel
      Left = 16
      Top = 168
      Width = 56
      Height = 15
      Caption = 'Cihaz ID:'
    end
    object edtBaseURL: TEdit
      Left = 16
      Top = 45
      Width = 602
      Height = 23
      TabOrder = 0
      Text = 'http://localhost/api/v1'
    end
    object edtEmail: TEdit
      Left = 16
      Top = 93
      Width = 602
      Height = 23
      TabOrder = 1
      Text = 'demo@example.com'
    end
    object edtSerialNumber: TEdit
      Left = 16
      Top = 141
      Width = 602
      Height = 23
      TabOrder = 2
      Text = 'DEMO-1234-5678-ABCD'
    end
    object edtDeviceID: TEdit
      Left = 96
      Top = 189
      Width = 522
      Height = 23
      Enabled = False
      TabOrder = 4
      Text = 'DEVICE-PC'
    end
    object chkUseDeviceID: TCheckBox
      Left = 16
      Top = 191
      Width = 74
      Height = 17
      Caption = 'Kullan'
      TabOrder = 3
      OnClick = chkUseDeviceIDClick
    end
  end
  object btnCheck: TButton
    Left = 8
    Top = 279
    Width = 634
    Height = 35
    Caption = 'Lisans'#305' Kontrol Et'
    Font.Charset = DEFAULT_CHARSET
    Font.Color = clWindowText
    Font.Height = -13
    Font.Name = 'Segoe UI'
    Font.Style = [fsBold]
    ParentFont = False
    TabOrder = 2
    OnClick = btnCheckClick
  end
  object grpResult: TGroupBox
    Left = 8
    Top = 320
    Width = 634
    Height = 222
    Caption = ' Sonu'#231' '
    TabOrder = 3
    object memoResult: TMemo
      Left = 2
      Top = 17
      Width = 630
      Height = 203
      Align = alClient
      Font.Charset = DEFAULT_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Consolas'
      Font.Style = []
      ParentFont = False
      ReadOnly = True
      ScrollBars = ssVertical
      TabOrder = 0
    end
  end
  object btnClear: TButton
    Left = 567
    Top = 279
    Width = 75
    Height = 35
    Caption = 'Temizle'
    TabOrder = 4
    OnClick = btnClearClick
  end
end
