@extends('layouts.app')

@section('title', 'Lisans Detayı')

@section('content')
<div class="mb-4">
    <a href="{{ route('dashboard.licenses.index') }}" class="text-blue-600 hover:text-blue-800">← Lisanslarıma Dön</a>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold mb-6">Lisans Detayı</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <p class="text-gray-600 text-sm">Seri Numarası:</p>
            <p class="font-semibold font-mono">{{ $license->serial_number }}</p>
        </div>

        <div>
            <p class="text-gray-600 text-sm">Durum:</p>
            @if($license->isValid())
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
            @else
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Geçersiz</span>
            @endif
        </div>

        <div>
            <p class="text-gray-600 text-sm">Ürün Paketi:</p>
            <p class="font-semibold">{{ $license->product_package }}</p>
        </div>

        <div>
            <p class="text-gray-600 text-sm">Lisans Tipi:</p>
            <p class="font-semibold">{{ ucfirst($license->license_type) }}</p>
        </div>

        <div>
            <p class="text-gray-600 text-sm">Başlangıç Tarihi:</p>
            <p class="font-semibold">{{ $license->starts_at->format('d.m.Y H:i') }}</p>
        </div>

        <div>
            <p class="text-gray-600 text-sm">Bitiş Tarihi:</p>
            @if($license->expires_at)
                <p class="font-semibold">{{ $license->expires_at->format('d.m.Y H:i') }}</p>
            @else
                <p class="font-semibold text-green-600">Ömür Boyu</p>
            @endif
        </div>

        @if($license->getDaysLeft() !== null)
        <div>
            <p class="text-gray-600 text-sm">Kalan Süre:</p>
            <p class="font-semibold {{ $license->isExpiringSoon() ? 'text-orange-600' : '' }}">
                {{ $license->getDaysLeft() }} gün
            </p>
        </div>
        @endif

        <div>
            <p class="text-gray-600 text-sm">Maksimum Bağlantı:</p>
            <p class="font-semibold">{{ $license->max_connection_count }}</p>
        </div>

        @if($license->device_id)
        <div>
            <p class="text-gray-600 text-sm">Bağlı Cihaz ID:</p>
            <p class="font-semibold font-mono text-sm">{{ $license->device_id }}</p>
        </div>
        @endif

        @if($license->last_checked_date)
        <div>
            <p class="text-gray-600 text-sm">Son Kontrol:</p>
            <p class="font-semibold">{{ $license->last_checked_date->format('d.m.Y H:i') }}</p>
        </div>
        @endif
    </div>

    @if($license->isExpiringSoon())
    <div class="mt-4 p-3 bg-orange-100 border border-orange-300 rounded text-orange-800">
        ⚠️ Lisansınız yakında sona erecek!
    </div>
    @endif

    @if(!$license->user_enable)
    <div class="mt-4 p-3 bg-red-100 border border-red-300 rounded text-red-800">
        ⚠️ Bu lisans devre dışı bırakılmış.
    </div>
    @endif

    @if($license->emergency)
    <div class="mt-4 p-3 bg-yellow-100 border border-yellow-300 rounded text-yellow-800">
        ⚠️ Bu lisans acil durum modunda.
    </div>
    @endif
</div>

@if($license->logs->count() > 0)
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4">Son Aktiviteler</h2>
    <div class="space-y-2">
        @foreach($license->logs as $log)
        <div class="border-l-4 pl-3 py-2 
            @if($log->level === 'error') border-red-500
            @elseif($log->level === 'info') border-blue-500
            @else border-gray-500
            @endif">
            <p class="text-sm">
                <span class="font-semibold">{{ $log->created_at->format('d.m.Y H:i') }}</span>
                - {{ $log->message }}
            </p>
        </div>
        @endforeach
    </div>
</div>
@endif

@endsection
