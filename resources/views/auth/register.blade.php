@extends('layouts.app')

@section('title', 'Üye Ol')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md">
        <div>
            <h2 class="text-center text-3xl font-extrabold text-gray-900">
                Yeni Hesap Oluştur
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Zaten hesabınız var mı?
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Giriş Yapın
                </a>
            </p>
        </div>

        <form method="POST" action="{{ route('register.post') }}" class="mt-8 space-y-6">
            @csrf

            <!-- Email Field (Required) -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                    E-posta Adresi *
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}"
                       required
                       autocomplete="email"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Field (Required) -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">
                    Şifre *
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       required
                       autocomplete="new-password"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Minimum 6 karakter</p>
            </div>

            <!-- Password Confirmation (Required) -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Şifre Tekrar *
                </label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       required
                       autocomplete="new-password"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Name Surname Field (Optional) -->
            <div>
                <label for="name_surname" class="block text-sm font-medium text-gray-700">
                    Ad Soyad
                </label>
                <input type="text" 
                       name="name_surname" 
                       id="name_surname" 
                       value="{{ old('name_surname') }}"
                       autocomplete="name"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name_surname') border-red-500 @enderror">
                @error('name_surname')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Company Field (Optional) -->
            <div>
                <label for="company" class="block text-sm font-medium text-gray-700">
                    Şirket
                </label>
                <input type="text" 
                       name="company" 
                       id="company" 
                       value="{{ old('company') }}"
                       autocomplete="organization"
                       class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company') border-red-500 @enderror">
                @error('company')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Kayıt Ol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
