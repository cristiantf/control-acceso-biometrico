<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar Sesión - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900" 
         style="background-image: linear-gradient(to bottom right, #3B82F6, #1E40AF);">

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-2xl overflow-hidden sm:rounded-lg">
            
            <div class="flex justify-center mb-6">
                <a href="/">
                    <svg class="h-16 w-auto text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M3 10.5a9 9 0 1118 0 9 9 0 01-18 0z" />
                    </svg>
                </a>
            </div>
            <h2 class="text-center text-2xl font-bold text-gray-700 dark:text-gray-200">Acceder a tu Cuenta</h2>

            <!-- Session Status -->
            <x-auth-session-status class="my-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="mt-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="dark:text-gray-300" />
                    <x-text-input id="email" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="dark:text-gray-300"/>
                    <x-text-input id="password" class="block mt-1 w-full dark:bg-gray-700 dark:text-gray-200"
                                    type="password"
                                    name="password"
                                    required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:focus:ring-offset-gray-800" name="remember">
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Recordarme') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('password.request') }}">
                            {{ __('¿Olvidaste tu contraseña?') }}
                        </a>
                    @endif

                    <x-primary-button class="ms-3 bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                        {{ __('Iniciar Sesión') }}
                    </x-primary-button>
                </div>

                <div class="text-center mt-6">
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900" href="{{ route('register') }}">
                        ¿No tienes una cuenta? Regístrate
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>