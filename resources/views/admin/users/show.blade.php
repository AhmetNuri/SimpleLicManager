@extends('layouts.app')

@section('title', 'Kullanıcı Detayları')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Kullanıcı Detayları</h1>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            Geri
        </a>
    </div>

    <div class="mb-6">
        <h2 class="text-lg font-semibold mb-4">Kullanıcı Bilgileri</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-gray-600 text-sm">ID:</p>
                <p class="font-semibold">{{ $user->id }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">E-posta:</p>
                <p class="font-semibold">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Kayıt Tarihi:</p>
                <p class="font-semibold">{{ $user->created_at->format('d.m.Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Son Güncelleme:</p>
                <p class="font-semibold">{{ $user->updated_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Lisanslar ({{ $user->licenses->count() }})</h2>
            <a href="{{ route('admin.licenses.create', ['user_id' => $user->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                Yeni Lisans Ekle
            </a>
        </div>
        
        @if($user->licenses->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seri No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bitiş Tarihi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($user->licenses as $license)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $license->serial_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $license->product_package }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $license->license_type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($license->isValid())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Geçersiz
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($license->expires_at)
                                {{ $license->expires_at->format('d.m.Y') }}
                                @if($license->isExpiringSoon())
                                    <span class="text-xs text-orange-600">({{ $license->getDaysLeft() }} gün kaldı)</span>
                                @endif
                            @else
                                <span class="text-green-600 font-semibold">Ömür Boyu</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.licenses.show', $license) }}" class="text-blue-600 hover:text-blue-900 mr-3">Görüntüle</a>
                            <a href="{{ route('admin.licenses.edit', $license) }}" class="text-green-600 hover:text-green-900">Düzenle</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Bu kullanıcının henüz lisansı yok.</p>
        @endif
    </div>
</div>
@endsection
