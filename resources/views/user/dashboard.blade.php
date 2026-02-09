@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Lisanslarım</h1>

    @if($licenses->count() > 0)
    <div class="grid gap-4">
        @foreach($licenses as $license)
        <div class="border rounded-lg p-4 {{ $license['is_valid'] ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-bold text-lg">{{ $license['product_package'] }}</h3>
                    <p class="text-sm text-gray-600 font-mono">{{ $license['serial_number'] }}</p>
                </div>
                <div>
                    @if($license['is_valid'])
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Geçersiz</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <p class="text-sm text-gray-600">Lisans Tipi:</p>
                    <p class="font-semibold">{{ ucfirst($license['license_type']) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Başlangıç:</p>
                    <p class="font-semibold">{{ $license['starts_at']->format('d.m.Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Bitiş:</p>
                    @if($license['expires_at'])
                        <p class="font-semibold">{{ $license['expires_at']->format('d.m.Y') }}</p>
                    @else
                        <p class="font-semibold text-green-600">Ömür Boyu</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kalan Süre:</p>
                    @if($license['days_left'] !== null)
                        <p class="font-semibold {{ $license['is_expiring_soon'] ? 'text-orange-600' : '' }}">
                            {{ $license['days_left'] }} gün
                        </p>
                    @else
                        <p class="font-semibold text-green-600">Sınırsız</p>
                    @endif
                </div>
            </div>

            @if($license['is_expiring_soon'])
            <div class="mt-3 p-2 bg-orange-100 border border-orange-300 rounded text-orange-800 text-sm">
                ⚠️ Lisansınız yakında sona erecek!
            </div>
            @endif

            @if(!$license['user_enable'])
            <div class="mt-3 p-2 bg-red-100 border border-red-300 rounded text-red-800 text-sm">
                ⚠️ Bu lisans devre dışı bırakılmış.
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <p class="text-gray-500 text-center py-8">Henüz lisansınız yok.</p>
    @endif
</div>
@endsection
