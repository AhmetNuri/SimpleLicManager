@extends('layouts.app')

@section('title', 'Lisanslarım')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Lisanslarım</h1>

    @if($licenses->count() > 0)
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seri No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ürün</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bitiş</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($licenses as $license)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-mono">{{ $license->serial_number }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm">{{ $license->product_package }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm">{{ ucfirst($license->license_type) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($license->isValid())
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Geçersiz</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($license->expires_at)
                            {{ $license->expires_at->format('d.m.Y') }}
                            @if($license->isExpiringSoon())
                                <span class="text-orange-600">({{ $license->getDaysLeft() }} gün)</span>
                            @endif
                        @else
                            <span class="text-green-600 font-semibold">Ömür Boyu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('dashboard.licenses.show', $license->id) }}" class="text-blue-600 hover:text-blue-800">Detay</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $licenses->links() }}
    </div>
    @else
    <p class="text-gray-500 text-center py-8">Henüz lisansınız yok.</p>
    @endif
</div>
@endsection
