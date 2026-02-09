@extends('layouts.app')

@section('title', 'Yeni Lisans')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Yeni Lisans Oluştur</h1>

    <form method="POST" action="{{ route('admin.licenses.store') }}">
        @csrf

        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Kullanıcı *</label>
                <select name="user_id" id="user_id" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <option value="">Seçiniz...</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id', request('user_id')) == $user->id ? 'selected' : '' }}>
                            {{ $user->email }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="serial_number" class="block text-gray-700 text-sm font-bold mb-2">Seri Numarası (otomatik)</label>
                <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" 
                       placeholder="Boş bırakılırsa otomatik oluşur"
                       class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>

            <div class="mb-4">
                <label for="product_package" class="block text-gray-700 text-sm font-bold mb-2">Ürün Paketi *</label>
                <input type="text" name="product_package" id="product_package" value="{{ old('product_package') }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>

            <div class="mb-4">
                <label for="license_type" class="block text-gray-700 text-sm font-bold mb-2">Lisans Tipi *</label>
                <select name="license_type" id="license_type" required class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    <option value="">Seçiniz...</option>
                    @foreach($licenseTypes as $type)
                        <option value="{{ $type->code }}" {{ old('license_type', 'monthly') == $type->code ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="starts_at" class="block text-gray-700 text-sm font-bold mb-2">Başlangıç Tarihi *</label>
                <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>

            <div class="mb-4">
                <label for="expires_at" class="block text-gray-700 text-sm font-bold mb-2">Bitiş Tarihi (ömür boyu için boş)</label>
                <input type="datetime-local" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                       class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>

            <div class="mb-4">
                <label for="device_id" class="block text-gray-700 text-sm font-bold mb-2">Cihaz ID (opsiyonel)</label>
                <input type="text" name="device_id" id="device_id" value="{{ old('device_id') }}"
                       class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>

            <div class="mb-4">
                <label for="max_connection_count" class="block text-gray-700 text-sm font-bold mb-2">Maks. Bağlantı *</label>
                <input type="number" name="max_connection_count" id="max_connection_count" value="{{ old('max_connection_count', 1) }}" required min="1"
                       class="shadow border rounded w-full py-2 px-3 text-gray-700">
            </div>
        </div>

        <div class="mb-4 flex gap-4">
            <label class="flex items-center">
                <input type="checkbox" name="user_enable" value="1" {{ old('user_enable', true) ? 'checked' : '' }} class="mr-2">
                <span class="text-sm text-gray-700">Kullanıcı Etkin</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="emergency" value="1" {{ old('emergency') ? 'checked' : '' }} class="mr-2">
                <span class="text-sm text-gray-700">Acil Durum</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('admin.licenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                İptal
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                Oluştur
            </button>
        </div>
    </form>
</div>
@endsection
