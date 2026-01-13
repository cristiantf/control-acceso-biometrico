<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Control Acceso Biométrico' }}</title>

        <!-- Scripts y Estilos -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
        <div class="min-h-screen flex flex-col">
            <!-- Enlace de retorno al Dashboard (opcional pero útil) -->
            <div class="bg-white dark:bg-gray-800 shadow p-4 text-right">
                <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-300 underline">Ir al Dashboard</a>
            </div>

            <!-- Contenido del Componente Livewire -->
            {{ $slot }}
        </div>
    </body>
</html>