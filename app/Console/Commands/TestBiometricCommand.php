<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\IsapiService;
use Illuminate\Support\Facades\Log;

class TestBiometricCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'biometric:test {action=check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta una prueba de integración contra el dispositivo biométrico a través de IsapiService. Acciones disponibles: check, users, open, logs';

    protected $isapiService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(IsapiService $isapiService)
    {
        parent::__construct();
        $this->isapiService = $isapiService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $this->info("Ejecutando acción de prueba: [{$action}]");

        try {
            switch ($action) {
                case 'check':
                    $this->testCheckConnection();
                    break;
                case 'users':
                    $this->testGetUsers();
                    break;
                case 'open':
                    $this->testOpenDoor();
                    break;
                case 'logs':
                    $this->testSearchLogs();
                    break;
                default:
                    $this->error("Acción '{$action}' no reconocida. Acciones disponibles: check, users, open, logs.");
                    return 1;
            }
            $this->info("Prueba finalizada exitosamente.");
        } catch (\Exception $e) {
            $this->error("Ocurrió un error durante la prueba: " . $e->getMessage());
            Log::error("Error en biometric:test -- " . $e->getMessage());
            return 1;
        }

        return 0;
    }

    private function testCheckConnection()
    {
        $this->line("Intentando verificar la conexión con el dispositivo...");
        $result = $this->isapiService->checkConnection();
        if ($result) {
            $this->info("¡Conexión exitosa!");
            $this->line("Respuesta XML recibida:");
            $this->line($result);
        } else {
            $this->error("Falló la conexión. Verifica la URL, credenciales, y la conexión de red.");
        }
    }

    private function testGetUsers()
    {
        $this->line("Intentando obtener la lista de usuarios...");
        $result = $this->isapiService->getUsers();
        if ($result) {
            $this->info("Respuesta recibida. Imprimiendo XML:");
            $this->line($result);
        } else {
            $this->error("No se pudo obtener la lista de usuarios.");
        }
    }

    private function testOpenDoor()
    {
        $this->line("Intentando enviar comando para abrir la puerta 1...");
        if ($this->confirm('¿Estás seguro de que quieres intentar abrir la puerta? Esta es una acción real.')) {
            $result = $this->isapiService->openDoor(1);
            if ($result) {
                $this->info("Comando de apertura enviado correctamente.");
            } else {
                $this->error("El comando de apertura de puerta falló.");
            }
        } else {
            $this->comment("Acción cancelada.");
        }
    }
    
    private function testSearchLogs()
    {
        $this->line("Buscando los últimos 5 eventos de acceso...");

        // Este XML es un ejemplo para buscar los últimos 5 eventos de control de acceso.
        // Puede que necesites ajustarlo según el modelo de tu dispositivo.
        $searchCriteriaXml = 
            '<CMSearchDescription version="1.0" xmlns="http://www.hikvision.com/ver10/XMLSchema">' .
            '<searchID>' . uniqid('search') . '</searchID>' .
            '<metaDataList>' .
            '<metaDataID>AccessControllerEvent</metaDataID>' .
            '</metaDataList>' .
            '<maxResults>5</maxResults>' .
            '<searchResultPostion>0</searchResultPostion>' .
            '</CMSearchDescription>';

        $result = $this->isapiService->searchLogs($searchCriteriaXml);

        if ($result) {
            $this->info("Respuesta de búsqueda de logs recibida. Imprimiendo XML:");
            $this->line($result);
        } else {
            $this->error("No se pudo obtener la respuesta de los logs.");
        }
    }
}
