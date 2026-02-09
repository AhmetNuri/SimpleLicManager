@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Profil Bilgileri</h1>

    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Hesap Bilgileri</h2>
        <div class="space-y-3">
            <div>
                <p class="text-gray-600 text-sm">E-posta:</p>
                <p class="font-semibold">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-gray-600 text-sm">Kayıt Tarihi:</p>
                <p class="font-semibold">{{ $user->created_at->format('d.m.Y H:i') }}</p>
            </div>
        </div>
    </div>

    <div>
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
</div>
@endsection
