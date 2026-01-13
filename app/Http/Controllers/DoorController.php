<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\IsapiService; // Importar el servicio
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Para registrar eventos

class DoorController extends Controller
{
    protected $isapiService;

    // Inyectar el servicio en el constructor
    public function __construct(IsapiService $isapiService)
    {
        $this->isapiService = $isapiService;
    }

    // Método para abrir la puerta
    public function open(Request $request)
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        $doorId = 1; // ID de la puerta a abrir (ajustar si es necesario)

        Log::info("Intento de apertura de puerta {$doorId} por usuario: {$user->email} (ID: {$user->id_usuario_app})");

        $success = $this->isapiService->openDoor($doorId);

        if ($success) {
            // Registrar el evento exitoso en la base de datos (Opcional aquí, podrías hacerlo en IsapiService)
            // \App\Models\RegistroAcceso::create([...]);
            Log::info("Puerta {$doorId} abierta exitosamente por {$user->email}.");
            // Redirigir de vuelta con mensaje de éxito
            return back()->with('success', 'Puerta abierta exitosamente.');
        } else {
            Log::error("Falló el intento de apertura de puerta {$doorId} por {$user->email}.");
            // Redirigir de vuelta con mensaje de error
            return back()->with('error', 'No se pudo abrir la puerta. Contacte al administrador.');
        }
    }
}
