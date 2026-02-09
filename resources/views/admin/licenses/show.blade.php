@extends('layouts.app')

@section('title', 'Lisans Detayları')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Lisans Detayları</h1>
        <div class="flex gap-2">
            <a href="{{ route('admin.licenses.edit', $license) }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                Düzenle
            </a>
            <a href="{{ route('admin.licenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                Geri
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
            <h2 class="text-lg font-semibold mb-4">Genel Bilgiler</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">ID:</p>
                    <p class="font-semibold">{{ $license->id }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Kullanıcı:</p>
                    <p class="font-semibold">{{ $license->user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Seri Numarası:</p>
                    <p class="font-mono text-sm">{{ $license->serial_number }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Ürün Paketi:</p>
                    <p class="font-semibold">{{ $license->product_package }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Lisans Tipi:</p>
                    <p class="font-semibold">{{ $license->license_type }}</p>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-4">Durum ve Tarihler</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-gray-600 text-sm">Durum:</p>
                    @if($license->isValid())
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Geçersiz</span>
                    @endif
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Başlangıç:</p>
                    <p class="font-semibold">{{ $license->starts_at->format('d.m.Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Bitiş:</p>
                    @if($license->expires_at)
                        <p class="font-semibold">{{ $license->expires_at->format('d.m.Y H:i') }}</p>
                        @if($license->getDaysLeft() !== null)
                            <p class="text-sm text-gray-600">({{ $license->getDaysLeft() }} gün kaldı)</p>
                        @endif
                    @else
                        <p class="font-semibold text-green-600">Ömür Boyu</p>
                    @endif
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Son Kontrol:</p>
                    <p class="font-semibold">{{ $license->last_checked_date ? $license->last_checked_date->format('d.m.Y H:i') : 'Hiç' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Cihaz ID:</p>
                    <p class="font-mono text-sm">{{ $license->device_id ?? 'Henüz bağlanmadı' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Son Kontrol Cihazı:</p>
                    <p class="font-mono text-sm">{{ $license->last_checked_device_id ?? 'Yok' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-6">
        <h2 class="text-lg font-semibold mb-4">Ayarlar</h2>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-gray-600 text-sm">Kullanıcı Etkin:</p>
                <p class="font-semibold">{{ $license->user_enable ? 'Evet' : 'Hayır' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Acil Durum:</p>
                <p class="font-semibold">{{ $license->emergency ? 'Evet' : 'Hayır' }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Max. Bağlantı:</p>
                <p class="font-semibold">{{ $license->max_connection_count }}</p>
            </div>
        </div>
    </div>

    <div>
        <h2 class="text-lg font-semibold mb-4">Son Loglar ({{ $license->logs->count() }})</h2>
        @if($license->logs->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Seviye</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mesaj</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($license->logs as $log)
                    <tr>
                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $log->created_at->format('d.m.Y H:i') }}</td>
                        <td class="px-4 py-2 whitespace-nowrap">
                            @if($log->level == 'error')
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">{{ $log->level }}</span>
                            @elseif($log->level == 'info')
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">{{ $log->level }}</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">{{ $log->level }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm">{{ $log->message }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Henüz log kaydı yok.</p>
        @endif
    </div>
</div>
@endsection
