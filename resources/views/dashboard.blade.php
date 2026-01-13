<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Panel de Control') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                 <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Panel de Acceso General -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Control de Acceso Rápido
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Utilice este botón para desbloquear el acceso principal.</p>
                    <form method="POST" action="{{ route('door.open') }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Abrir Puerta Principal
                        </button>
                    </form>
                </div>
            </div>

            <!-- Paneles Específicos por Rol -->
            @if (Auth::user()->id_rol == 1)
                <!-- Panel de Administrador -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-blue-50 dark:bg-gray-700/50 border-l-4 border-blue-500">
                        <h3 class="text-xl font-bold text-blue-900 dark:text-blue-300 mb-2">
                            Panel de Administración
                        </h3>
                        <p class="text-gray-700 dark:text-gray-400 mb-6">Gestione usuarios, consulte registros y configure el sistema.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <a href="{{ route('personal.index') }}" class="bg-blue-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-blue-200 p-4 rounded-lg flex items-center transition">
                                <svg class="h-8 w-8 text-blue-600 dark:text-blue-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197M15 21a6 6 0 006-6v-1a3 3 0 00-3-3H9a3 3 0 00-3 3v1a6 6 0 006 6z" /></svg>
                                <span class="font-semibold text-blue-800 dark:text-gray-200">Gestionar Personal</span>
                            </a>
                            <a href="#" class="bg-blue-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-blue-200 p-4 rounded-lg flex items-center transition">
                                <svg class="h-8 w-8 text-blue-600 dark:text-blue-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <span class="font-semibold text-blue-800 dark:text-gray-200">Ver Registros de Acceso</span>
                            </a>
                            <a href="#" class="bg-blue-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-blue-200 p-4 rounded-lg flex items-center transition">
                                <svg class="h-8 w-8 text-blue-600 dark:text-blue-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                <span class="font-semibold text-blue-800 dark:text-gray-200">Configuración</span>
                            </a>
                        </div>
                    </div>
                </div>
            @elseif (Auth::user()->id_rol == 2)
                <!-- Panel de Docente -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-green-50 dark:bg-gray-700/50 border-l-4 border-green-500">
                        <h3 class="text-xl font-bold text-green-900 dark:text-green-300 mb-2">
                            Panel del Docente
                        </h3>
                        <p class="text-gray-700 dark:text-gray-400 mb-6">Herramientas y accesos rápidos para sus actividades diarias.</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="#" class="bg-green-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-green-200 p-4 rounded-lg flex items-center transition">
                                <svg class="h-8 w-8 text-green-600 dark:text-green-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span class="font-semibold text-green-800 dark:text-gray-200">Mis Horarios</span>
                            </a>
                            <a href="#" class="bg-green-100 dark:bg-gray-800 dark:hover:bg-gray-700 hover:bg-green-200 p-4 rounded-lg flex items-center transition">
                                <svg class="h-8 w-8 text-green-600 dark:text-green-400 mr-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v11.494m-9-5.747h18" /></svg>
                                <span class="font-semibold text-green-800 dark:text-gray-200">Registrar Asistencia</span>
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <!-- Panel de Usuario General -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="font-semibold text-lg">¡Bienvenido, {{ Auth::user()->name }}!</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-2">
                            No tienes un rol específico asignado. Puedes usar las funciones de acceso rápido.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
