@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h1 class="text-2xl font-bold mb-6">Profil Bilgileri</h1>

    <form method="POST" action="{{ route('dashboard.profile.update') }}" class="max-w-md mb-8">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                E-posta *
            </label>
            <input type="email" 
                   name="email" 
                   id="email" 
                   value="{{ old('email', $user->email) }}"
                   required
                   class="shadow border rounded w-full py-2 px-3 text-gray-700">
            @error('email')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="name_surname" class="block text-gray-700 text-sm font-bold mb-2">
                Ad Soyad
            </label>
            <input type="text" 
                   name="name_surname" 
                   id="name_surname" 
                   value="{{ old('name_surname', $user->name_surname) }}"
                   class="shadow border rounded w-full py-2 px-3 text-gray-700">
        </div>

        <div class="mb-4">
            <label for="company" class="block text-gray-700 text-sm font-bold mb-2">
                Şirket
            </label>
            <input type="text" 
                   name="company" 
                   id="company" 
                   value="{{ old('company', $user->company) }}"
                   class="shadow border rounded w-full py-2 px-3 text-gray-700">
        </div>

        <div class="mb-4">
            <p class="text-gray-600 text-sm">Rol:</p>
            <p class="font-semibold">{{ $user->role === 'admin' ? 'Admin' : 'Müşteri' }}</p>
        </div>

        <div class="mb-4">
            <p class="text-gray-600 text-sm">Kayıt Tarihi:</p>
            <p class="font-semibold">{{ $user->created_at->format('d.m.Y H:i') }}</p>
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Profili Güncelle
        </button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-lg font-semibold mb-4">Şifre Değiştir</h2>
    <form method="POST" action="{{ route('dashboard.profile.password') }}" class="max-w-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2">
                Mevcut Şifre *
            </label>
            <input type="password" 
                   name="current_password" 
                   id="current_password" 
                   required
                   class="shadow border rounded w-full py-2 px-3 text-gray-700">
            @error('current_password')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                Yeni Şifre * (min. 8 karakter)
            </label>
            <input type="password" 
                   name="password" 
                   id="password" 
                   required
                   class="shadow border rounded w-full py-2 px-3 text-gray-700">
            @error('password')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                Yeni Şifre Tekrar *
            </label>
            <input type="password" 
                   name="password_confirmation" 
                   id="password_confirmation" 
                   required
                   class="shadow border rounded w-full py-2 px-3 text-gray-700">
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Şifreyi Güncelle
        </button>
    </form>
</div>
@endsection
