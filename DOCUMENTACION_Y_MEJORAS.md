# Documentación y Propuesta de Mejoras del Sistema de Control de Acceso

## 1. Descripción General del Proyecto

Este proyecto es una aplicación web desarrollada en Laravel que actúa como interfaz para un sistema de control de acceso biométrico, presumiblemente de la marca Hikvision. El sistema permite a los usuarios autenticados realizar acciones como abrir una puerta de forma remota.

La aplicación está diseñada para ser un punto de gestión centralizado, con potencial para expandirse y manejar múltiples dispositivos, usuarios y registros de acceso.

### Funcionalidad Principal Actual
- Autenticación de usuarios (Login, Registro).
- Un panel de control (dashboard) desde donde los usuarios pueden interactuar con el sistema.
- Acción remota para abrir una puerta principal.
- Paneles de control diferenciados por roles (Administrador, Docente y usuario general).

## 2. Arquitectura y Componentes Clave

El sistema sigue una arquitectura estándar de Laravel (MVC) y se integra con un dispositivo de hardware a través de un servicio dedicado.

- **`app/Services/IsapiService.php`**: Este es el núcleo de la integración. Es una clase de servicio que encapsula toda la comunicación con el dispositivo biométrico usando el protocolo ISAPI de Hikvision. Utiliza autenticación `Digest` y el cliente HTTP de Laravel para enviar comandos GET, POST, PUT, etc.

- **`app/Http/Controllers/DoorController.php`**: Un controlador que maneja las solicitudes HTTP del usuario para abrir la puerta. Utiliza `IsapiService` para ejecutar la acción y devuelve una respuesta al usuario.

- **`routes/web.php`**: Define las rutas accesibles para los usuarios, incluyendo la ruta `POST /door/open` que activa el `DoorController`.

- **Modelos**:
    - `User`: Modelo estándar de usuario de Laravel, con un campo `id_rol` para un sistema simple de manejo de roles.
    - `Role`: (No implementado completamente en la lógica) Modelo para definir los roles.
    - `RegistroAcceso` y `PersonalEnBiometrico`: Modelos preparados para futuras funcionalidades de gestión de logs y personal.

- **Vistas (`resources/views`)**: Las vistas de Blade definen la interfaz de usuario. Se ha implementado un diseño moderno y responsivo con Tailwind CSS, incluyendo una página de bienvenida, login/registro y un dashboard con paneles dinámicos según el rol del usuario.

## 3. Guía de Instalación y Configuración

Para desplegar este proyecto en un entorno de desarrollo local, sigue estos pasos:

1.  **Clonar el Repositorio**:
    ```bash
    git clone <url-del-repositorio>
    cd control-acceso-biometrico
    ```

2.  **Instalar Dependencias**:
    ```bash
    composer install
    npm install
    ```

3.  **Configurar el Entorno**:
    - Copia el archivo de ejemplo `.env.example` a `.env`.
      ```bash
      cp .env.example .env
      ```
    - Genera la clave de la aplicación.
      ```bash
      php artisan key:generate
      ```
    - Configura la base de datos en el archivo `.env` (por ejemplo, usando SQLite, MySQL, etc.).

4.  **Configurar la Conexión al Dispositivo Biométrico**:
    - Añade las siguientes variables al final de tu archivo `.env` con las credenciales correctas de tu dispositivo Hikvision.
      ```env
      HIKVISION_BASE_URL=http://192.168.1.64
      HIKVISION_USERNAME=admin
      HIKVISION_PASSWORD=your_device_password
      ```
    - Asegúrate de que el archivo `config/services.php` esté configurado para leer estas variables:
      ```php
      'hikvision' => [
          'base_url' => env('HIKVISION_BASE_URL'),
          'username' => env('HIKVISION_USERNAME'),
          'password' => env('HIKVISION_PASSWORD'),
      ],
      ```

5.  **Ejecutar Migraciones y Seeders**:
    - Para crear las tablas de la base de datos y (opcionalmente) llenarlas con datos de prueba:
      ```bash
      php artisan migrate --seed
      ```

6.  **Compilar Assets y Ejecutar el Servidor**:
    ```bash
    npm run dev
    php artisan serve
    ```

## 4. Propuesta de Mejoras

La base del proyecto es sólida, pero se puede expandir considerablemente para crear un sistema de control de acceso mucho más robusto y completo.

### Mejoras de Funcionalidad
1.  **Gestión Completa de Personal**:
    - Implementar las funciones `getUsers`, `addUser` y `deleteUser` del `IsapiService`.
    - Crear interfaces en el panel de administrador para listar, añadir (con nombre, ID, etc.) y eliminar usuarios del dispositivo biométrico directamente desde la aplicación web.

2.  **Visualizador de Registros de Acceso**:
    - Implementar la función `searchLogs` del `IsapiService`.
    - Crear una vista en el panel de administrador donde se puedan buscar y visualizar los eventos de acceso (quién abrió la puerta, a qué hora, intentos fallidos, etc.). Permitir filtrar por fecha, usuario, etc.

3.  **Escucha de Eventos en Tiempo Real**:
    - Implementar la función `listenForEvents` como un `Job` de Laravel que se ejecute en segundo plano.
    - Esto permitiría recibir notificaciones en tiempo real (e.g., usando WebSockets con Laravel Echo) cuando ocurra un evento en el dispositivo, como una apertura de puerta manual, y mostrarlo en el dashboard sin necesidad de recargar la página.

### Mejoras de Arquitectura y Código
4.  **Sistema de Autorización Robusto**:
    - Reemplazar el chequeo simple `Auth::user()->id_rol == 1` por el sistema de Gates y Policies de Laravel.
    - Definir `Policies` para acciones como `open-door`, `view-users`, `manage-system`, etc. Esto hace que los permisos sean más granulares, legibles y fáciles de mantener.

5.  **Pruebas (Testing)**:
    - Escribir pruebas unitarias para el `IsapiService`, simulando (`mocking`) las respuestas del cliente HTTP para no depender del dispositivo real durante las pruebas.
    - Escribir pruebas de funcionalidad (`Feature tests`) para `DoorController` y los flujos de autenticación para asegurar que las rutas y respuestas funcionen como se espera.

6.  **Validación y Manejo de Errores**:
    - Mejorar la validación en el constructor de `IsapiService`. En lugar de solo registrar un error, podría lanzar una excepción personalizada (e.g., `HikvisionConnectionException`) si las credenciales no están configuradas, para detener la ejecución de forma controlada.
    - Añadir más `try-catch` y validación de respuestas XML/JSON del dispositivo para manejar casos inesperados.

### Mejoras de Interfaz de Usuario
7.  **Refinamiento de la Interfaz de Roles**:
    - Desarrollar las vistas para las funcionalidades propuestas (gestión de usuarios, logs, etc.).
    - Crear componentes de Blade reutilizables para elementos comunes de la interfaz (tablas, modales de confirmación, etc.).

8.  **Notificaciones en la Interfaz**:
    - Integrar las respuestas de éxito o error (e.g., 'Puerta abierta') como notificaciones "Toast" no intrusivas en lugar de recargar la página con un mensaje de sesión. Se puede usar Livewire o una librería simple de JavaScript para esto.

## 5. Explicación del `IsapiService` y Pruebas de Integración

### ¿Qué es `IsapiService`?
El `IsapiService` es el corazón de la comunicación entre esta aplicación Laravel y el dispositivo biométrico Hikvision. Su propósito es abstraer la complejidad del protocolo ISAPI (Hikvision's API) en métodos PHP simples y reutilizables como `checkConnection()`, `getUsers()` y `openDoor()`.

Cada vez que llamas a uno de estos métodos, el servicio:
1. Construye la URL correcta del endpoint de la API del dispositivo.
2. Utiliza el cliente HTTP de Laravel para realizar una petición con autenticación `Digest`, que es el método de seguridad que usan estos dispositivos.
3. Maneja la respuesta del dispositivo, que usualmente es un string en formato XML, y la devuelve.
4. Registra errores si la petición falla por cualquier motivo (mala conexión, credenciales incorrectas, etc.).

### ¿Cómo realizar Pruebas de Integración?

Las pruebas de integración son cruciales para asegurar que la aplicación se comunica correctamente con el hardware. A diferencia de las pruebas unitarias (que simulan el dispositivo), estas pruebas envían comandos **reales** al dispositivo biométrico.

Se ha creado un comando de Artisan para facilitar estas pruebas de forma segura desde la terminal.

**Pre-requisitos:**
1. Asegúrate de que tu aplicación Laravel se está ejecutando en una red que tiene acceso al dispositivo biométrico.
2. Verifica que las credenciales en tu archivo `.env` son correctas:
   ```env
   HIKVISION_BASE_URL=http://<IP_DEL_DISPOSITIVO>
   HIKVISION_USERNAME=<TU_USUARIO_ADMIN>
   HIKVISION_PASSWORD=<TU_CONTRASEÑA>
   ```

**Uso del Comando de Prueba:**

El comando `php artisan biometric:test {accion}` permite invocar diferentes métodos del `IsapiService`.

1.  **Verificar Conexión (`check`):**
    Es la prueba más segura y fundamental. Simplemente consulta la información del dispositivo.
    ```bash
    php artisan biometric:test check
    ```
    - **Resultado esperado**: Un mensaje de "¡Conexión exitosa!" seguido del XML con la información del dispositivo (`<DeviceInfo>...</DeviceInfo>`).
    - **Si falla**: Revisa la IP, el puerto, las credenciales y que no haya un firewall bloqueando la conexión.

2.  **Obtener Usuarios (`users`):**
    Este comando intenta obtener la lista de usuarios configurados en el dispositivo.
    ```bash
    php artisan biometric:test users
    ```
    - **Resultado esperado**: Un XML (`<UserList>...</UserList>`) con la información de los usuarios. Esto es útil para entender la estructura de datos que devuelve el dispositivo.

3.  **Buscar Logs (`logs`):**
    Busca los últimos 5 eventos de acceso registrados en el terminal.
    ```bash
    php artisan biometric:test logs
    ```
    - **Resultado esperado**: Un XML (`<CMSearchResult>...</CMSearchResult>`) con los eventos. Útil para ver qué tipo de información puedes obtener.

4.  **Abrir la Puerta (`open`):**
    > **¡ADVERTENCIA!** Esta es una acción real que desbloqueará físicamente la puerta conectada al dispositivo. Úsalo con precaución.

    ```bash
    php artisan biometric:test open
    ```
    - El comando te pedirá confirmación antes de ejecutarse.
    - **Resultado esperado**: Un mensaje de "Comando de apertura enviado correctamente."

### Creación de Nuevos Usuarios Administradores

Para facilitar el acceso, se han creado "seeders" para añadir usuarios con roles predefinidos a la base de datos. Para crear un usuario administrador y un docente, ejecuta:

```bash
php artisan db:seed
```

Esto creará dos usuarios:
- **Administrador**: `admin@example.com` / `password`
- **Docente**: `docente@example.com` / `password`

Puedes usar estas credenciales para iniciar sesión y probar los diferentes paneles de rol.
