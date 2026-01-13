<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Control de Acceso Biométrico</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-gray-900 selection:bg-blue-500 selection:text-white">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-800 opacity-90"></div>
        
        @if (Route::has('login'))
            <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">
                @auth
                    <a href="{{ url('/dashboard') }}" class="font-semibold text-white hover:text-gray-200 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-blue-500">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="font-semibold text-white hover:text-gray-200 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-blue-500">Iniciar Sesión</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="ml-4 font-semibold text-white hover:text-gray-200 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-blue-500">Registrarse</a>
                    @endif
                @endauth
            </div>
        @endif

        <div class="max-w-7xl mx-auto p-6 lg:p-8 z-10">
            <div class="flex justify-center">
                <svg class="h-24 w-auto text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3.75 3.75M12 9.75l3.75 3.75M3 10.5a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
            </div>

            <div class="mt-8 text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight">
                    Sistema de Control de Acceso Biométrico
                </h1>
                <p class="mt-6 text-lg text-blue-100 max-w-2xl mx-auto">
                    Gestión centralizada y segura para el acceso a instalaciones. Abra puertas de forma remota, administre personal y consulte registros con facilidad.
                </p>
            </div>

            <div class="mt-12 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="inline-block bg-white text-blue-700 font-semibold rounded-lg px-8 py-4 text-lg hover:bg-gray-100 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg">
                    Acceder al Panel
                </a>
                <a href="#" class="font-semibold text-white hover:underline">
                    Contactar a Soporte
                </a>
            </div>

            <div class="mt-16 text-center text-sm text-blue-200">
                <p>&copy; {{ date('Y') }} Institución Educativa. Todos los derechos reservados.</p>
            </div>
        </div>
    </div>
</body>
</html>