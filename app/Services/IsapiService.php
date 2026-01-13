<?php

namespace App\Services;

use Illuminate\Support\Facades\Http; // Usaremos el cliente HTTP de Laravel (basado en Guzzle)
use Illuminate\Support\Facades\Log;

class IsapiService
{
    protected $baseUrl;
    protected $username;
    protected $password;

    public function __construct()
    {
        // Cargar desde .env para seguridad y flexibilidad
        $this->baseUrl = config('services.hikvision.base_url'); // Ej: http://192.168.1.64
        $this->username = config('services.hikvision.username');
        $this->password = config('services.hikvision.password');

        if (!$this->baseUrl || !$this->username || !$this->password) {
            Log::error('Credenciales o URL base de Hikvision no configuradas en config/services.php o .env');
            // Considera lanzar una excepción si la configuración es crucial
        }
    }

    /**
     * Realiza una petición ISAPI usando autenticación Digest.
     */
    private function makeRequest($method, $endpoint, $options = [])
    {
        if (!$this->baseUrl) return null; // O lanzar excepción

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        try {
            $response = Http::withDigestAuth($this->username, $this->password)
                ->timeout(10); // Timeout de 10 segundos

            switch (strtoupper($method)) {
                case 'GET':
                    // Guzzle pasa 'query' para parámetros GET
                    if (isset($options['query'])) {
                       $response = $response->get($url, $options['query']);
                    } else {
                       $response = $response->get($url);
                    }
                    break;
                case 'PUT':
                    // Guzzle usa 'body' para el cuerpo PUT/POST con Content-Type correcto
                    if (isset($options['body'])) {
                        // Determinar Content-Type basado en $options si es necesario (XML vs JSON vs octet-stream)
                        $contentType = $options['contentType'] ?? 'application/xml; charset="UTF-8"';
                        $response = $response->withBody($options['body'], $contentType)->put($url);
                    } else {
                        $response = $response->put($url);
                    }
                    break;
                case 'POST':
                     if (isset($options['body'])) {
                        $contentType = $options['contentType'] ?? 'application/xml; charset="UTF-8"';
                        $response = $response->withBody($options['body'], $contentType)->post($url);
                    } else {
                        $response = $response->post($url);
                    }
                    break;
                case 'DELETE':
                    $response = $response->delete($url);
                    break;
                default:
                    Log::error("Método HTTP no soportado: {$method}");
                    return null;
            }

            if (!$response->successful()) {
                Log::error("Error en petición ISAPI a {$url}: Status {$response->status()} - Body: " . $response->body());
                // Puedes lanzar una excepción personalizada aquí si prefieres
                return null; // O un array indicando error ['error' => true, 'status' => $response->status(), 'body' => $response->body()]
            }

            // Devolver el cuerpo de la respuesta (podría ser XML, JSON, etc.)
            return $response->body();

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error("Error de conexión ISAPI a {$url}: " . $e->getMessage());
            return null; // O un array indicando error de conexión
        } catch (\Exception $e) {
            Log::error("Error general en petición ISAPI a {$url}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Intenta abrir la puerta remotamente.
     * Endpoint y formato del cuerpo son placeholders - ¡NECESITAN VERIFICACIÓN!
     */
    public function openDoor($doorId = 1) // Asumiendo ID de puerta 1 por defecto
    {
        // **PLACEHOLDER - Endpoint y Body REQUIEREN VERIFICACIÓN**
        // Opción 1: Comando específico de control de acceso (preferido)
        // $endpoint = "/ISAPI/AccessControl/RemoteControl/door/{$doorId}/open";
        // $response = $this->makeRequest('PUT', $endpoint); // Puede no necesitar body

        // Opción 2: Trigger de salida genérico (si la cerradura está cableada como salida)
        $outputId = 1; // ID de la salida conectada a la cerradura (¡Verificar!)
        $endpoint = "/ISAPI/ContentMgmt/IOProxy/outputs/{$outputId}/trigger";
        // El body puede variar, podría ser algo así para activar momentáneamente
        $body = '<IOPortData version="2.0" xmlns="http://www.isapi.org/ver20/XMLSchema"><outputState>pulse</outputState></IOPortData>'; // O estado 'high'/'low'
        $response = $this->makeRequest('PUT', $endpoint, ['body' => $body, 'contentType' => 'application/xml']);

        // Interpretar la respuesta (usualmente XML_ResponseStatus)
        if ($response && str_contains($response, '<statusCode>1</statusCode>')) {
            Log::info("Comando OpenDoor enviado exitosamente a la puerta/salida {$doorId}/{$outputId}.");
            return true;
        } else {
            Log::error("Falló el envío del comando OpenDoor a la puerta/salida {$doorId}/{$outputId}. Respuesta: " . $response);
            return false;
        }
    }

    /**
     * Verifica la conexión y autenticación con el dispositivo.
     */
    public function checkConnection()
    {
        // Usar un endpoint simple como /ISAPI/System/deviceInfo
        $response = $this->makeRequest('GET', '/ISAPI/System/deviceInfo');
        return $response !== null;
    }

     /**
     * Obtiene la lista de usuarios del dispositivo.
     */
    public function getUsers()
    {
        return $this->makeRequest('GET', '/ISAPI/Security/users');
    }

    /**
     * Añade un usuario al dispositivo.
     * @param array $userData ['employeeNo' => string, 'name' => string]
     * @return bool
     */
    public function addUser(array $userData): bool
    {
        $employeeNo = $userData['employeeNo'];
        $name = $userData['name'];

        $xmlPayload = <<<XML
<User>
    <employeeNo>{$employeeNo}</employeeNo>
    <name>{$name}</name>
    <userType>normalUser</userType>
    <Valid>
        <enable>true</enable>
        <beginTime>2024-01-01T00:00:00</beginTime>
        <endTime>2037-12-31T23:59:59</endTime>
    </Valid>
</User>
XML;

        $response = $this->makeRequest('POST', '/ISAPI/Security/users', ['body' => $xmlPayload]);

        // Un '200 OK' con un body vacío o un ResponseStatus exitoso es común para POST.
        return $response !== null && (empty($response) || str_contains($response, '<statusCode>1</statusCode>'));
    }

    /**
     * Actualiza un usuario existente en el dispositivo.
     * @param array $userData ['employeeNo' => string, 'name' => string]
     * @return bool
     */
    public function updateUser(array $userData): bool
    {
        $employeeNo = $userData['employeeNo'];
        $name = $userData['name'];

        $xmlPayload = <<<XML
<User>
    <employeeNo>{$employeeNo}</employeeNo>
    <name>{$name}</name>
    <userType>normalUser</userType>
    <Valid>
        <enable>true</enable>
        <beginTime>2024-01-01T00:00:00</beginTime>
        <endTime>2037-12-31T23:59:59</endTime>
    </Valid>
</User>
XML;

        $response = $this->makeRequest('PUT', "/ISAPI/Security/users/{$employeeNo}", ['body' => $xmlPayload]);

        return $response !== null && (empty($response) || str_contains($response, '<statusCode>1</statusCode>'));
    }

    /**
     * Elimina un usuario del dispositivo por su ID (employeeNo).
     * @param string $employeeNo
     * @return bool
     */
    public function deleteUser(string $employeeNo): bool
    {
        $response = $this->makeRequest('DELETE', "/ISAPI/Security/users/{$employeeNo}");
        
        // La respuesta a DELETE exitoso suele ser un XML de ResponseStatus.
        return $response !== null && str_contains($response, '<statusCode>1</statusCode>');
    }

    /**
     * Obtiene logs/eventos del dispositivo.
     * // Requiere definir $searchCriteriaXml basado en <CMSearchDescription>
     */
     public function searchLogs($searchCriteriaXml)
     {
         // Endpoint para buscar logs generales o logs de seguridad
         return $this->makeRequest('POST', '/ISAPI/ContentMgmt/logSearch', ['body' => $searchCriteriaXml]);
         // O usar /ISAPI/ContentMgmt/security/logSearch para logs de seguridad
     }

     /**
      * (Alternativa para historial) Escucha eventos en tiempo real.
      * Esta función necesitaría ejecutarse continuamente o como un job.
      * Implementación más compleja que buscar logs.
      */
     public function listenForEvents()
     {
        // Se conecta a /ISAPI/Event/notification/alertStream
        // Requiere manejo de conexión persistente y parsing de stream multipart.
        // GuzzleHttp/Client puede manejar streams, pero requiere lógica adicional.
        Log::warning('listenForEvents no implementado completamente.');
        return false;
     }

}