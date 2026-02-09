@extends('layouts.app')

@section('title', 'Lisanslar')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Lisanslar</h1>
        <a href="{{ route('admin.licenses.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Yeni Lisans
        </a>
    </div>

    <div class="mb-4">
        <form method="GET" action="{{ route('admin.licenses.index') }}" class="flex gap-2">
            <input type="text" 
                   name="search" 
                   value="{{ $search }}" 
                   placeholder="Seri no, paket veya e-posta ile ara..." 
                   class="flex-1 border rounded px-4 py-2">
            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">
                Ara
            </button>
            @if($search)
                <a href="{{ route('admin.licenses.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">
                    Temizle
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seri No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tip</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bitiş</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($licenses as $license)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $license->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $license->user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $license->serial_number }}</td>
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
                        @else
                            <span class="text-green-600">Ömür Boyu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.licenses.show', $license) }}" class="text-blue-600 hover:text-blue-900 mr-3">Görüntüle</a>
                        <a href="{{ route('admin.licenses.edit', $license) }}" class="text-green-600 hover:text-green-900 mr-3">Düzenle</a>
                        <form method="POST" action="{{ route('admin.licenses.destroy', $license) }}" class="inline" onsubmit="return confirm('Bu lisansı silmek istediğinize emin misiniz?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Sil</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Lisans bulunamadı.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $licenses->links() }}
    </div>
</div>
@endsection
