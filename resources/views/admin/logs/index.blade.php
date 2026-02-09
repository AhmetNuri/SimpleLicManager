@extends('layouts.app')

@section('title', 'Sistem Logları')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Sistem Logları</h1>

    <div class="mb-4 flex gap-2">
        <form method="GET" action="{{ route('admin.logs.index') }}" class="flex gap-2 flex-1">
            <select name="level" class="border rounded px-4 py-2">
                <option value="">Tüm Seviyeler</option>
                <option value="info" {{ $level == 'info' ? 'selected' : '' }}>Info</option>
                <option value="debug" {{ $level == 'debug' ? 'selected' : '' }}>Debug</option>
                <option value="error" {{ $level == 'error' ? 'selected' : '' }}>Error</option>
            </select>
            <input type="text" name="search" value="{{ $search }}" placeholder="Mesaj ara..." class="flex-1 border rounded px-4 py-2">
            <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded">Filtrele</button>
            @if($level || $search)
                <a href="{{ route('admin.logs.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded">Temizle</a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarih</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Seviye</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lisans ID</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kullanıcı</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mesaj</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($logs as $log)
                <tr>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        @if($log->level == 'error')
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-red-100 text-red-800">{{ $log->level }}</span>
                        @elseif($log->level == 'info')
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">{{ $log->level }}</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded bg-gray-100 text-gray-800">{{ $log->level }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        @if($log->license_id)
                            <a href="{{ route('admin.licenses.show', $log->license_id) }}" class="text-blue-600 hover:text-blue-900">
                                #{{ $log->license_id }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                        @if($log->user)
                            <a href="{{ route('admin.users.show', $log->user_id) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $log->user->email }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">{{ $log->message }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-4 text-center text-gray-500">Log kaydı bulunamadı.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection
