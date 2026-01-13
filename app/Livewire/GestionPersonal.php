<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Services\IsapiService;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GestionPersonal extends Component
{
    use WithPagination;

    public $isOpen = false;
    public $search = '';
    public $userId, $name, $email, $password, $role_id;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
            'role_id' => 'required|in:1,2', // 1: Admin, 2: Docente
            'password' => [$this->userId ? 'nullable' : 'required', 'string', 'min:8'],
        ];
    }

    public function render()
    {
        $users = User::where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->paginate(10);

        return view('livewire.gestion-personal', [
            'users' => $users,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    private function resetInputFields()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role_id = 2; // Por defecto Docente
    }

    public function store()
    {
        $this->validate();

        $isNewUser = !$this->userId;

        // Crear o actualizar el usuario en la BD local
        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $this->name,
                'email' => $this->email,
                'id_rol' => $this->role_id,
                'password' => Hash::make($this->password),
            ]
        );

        // Si es un usuario nuevo, usar su ID de la BD como employee_no
        if ($isNewUser) {
            $user->employee_no = (string) $user->id;
            $user->save();
        }

        // Sincronizar con el dispositivo biométrico
        $isapiService = new IsapiService();
        if ($isNewUser) {
            $syncSuccess = $isapiService->addUser([
                'employeeNo' => $user->employee_no,
                'name' => $user->name,
            ]);
        } else {
            $syncSuccess = $isapiService->updateUser([
                'employeeNo' => $user->employee_no,
                'name' => $user->name,
            ]);
        }

        if ($syncSuccess) {
            session()->flash('message', 'Usuario ' . ($isNewUser ? 'creado' : 'actualizado') . ' y sincronizado con el biométrico.');
        } else {
            session()->flash('error', 'Usuario ' . ($isNewUser ? 'creado' : 'actualizado') . ' localmente, pero falló la sincronización con el biométrico.');
        }

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role_id = $user->id_rol;
        $this->password = '';

        $this->openModal();
    }

    public function delete($id)
    {
        $user = User::find($id);

        if (!$user) {
            session()->flash('error', 'No se encontró el usuario.');
            return;
        }

        $syncSuccess = false;
        if ($user->employee_no) {
            $isapiService = new IsapiService();
            $syncSuccess = $isapiService->deleteUser($user->employee_no);
        }

        if ($syncSuccess) {
            $user->delete();
            session()->flash('message', 'Usuario eliminado localmente y del biométrico.');
        } else {
            // Decisión de diseño: ¿eliminar localmente aunque falle en el biométrico?
            // Por ahora, lo eliminamos localmente pero advertimos del fallo.
            $user->delete();
            session()->flash('error', 'Usuario eliminado localmente, pero no se pudo eliminar del biométrico. Puede que necesite borrarlo manualmente desde el dispositivo.');
        }
    }

    public function testConnection()
    {
        $isapiService = new IsapiService();
        if ($isapiService->checkConnection()) {
            session()->flash('message', '¡Conexión con el dispositivo biométrico exitosa!');
        } else {
            session()->flash('error', 'Error: No se pudo conectar con el dispositivo biométrico. Verifique la configuración y la red.');
        }
    }

    public function importUsers()
    {
        $isapiService = new IsapiService();
        $xmlString = $isapiService->getUsers();

        if (!$xmlString) {
            session()->flash('error', 'No se pudo obtener la lista de usuarios del dispositivo.');
            return;
        }

        try {
            $xml = new \SimpleXMLElement($xmlString);
            $importedCount = 0;
            foreach ($xml->User as $deviceUser) {
                // Asumimos una estructura de email basada en el nombre
                $email = strtolower(str_replace(' ', '.', (string)$deviceUser->name)) . '@institucion.com';

                User::updateOrCreate(
                    ['employee_no' => (string)$deviceUser->employeeNo],
                    [
                        'name' => (string)$deviceUser->name,
                        'email' => $email,
                        'password' => Hash::make(Str::random(10)), // Contraseña aleatoria
                        'id_rol' => 2, // Asignar rol de Docente por defecto
                    ]
                );
                $importedCount++;
            }
            session()->flash('message', "Se importaron o actualizaron {$importedCount} usuarios.");
        } catch (\Exception $e) {
            session()->flash('error', 'Error al procesar el XML de usuarios: ' . $e->getMessage());
        }
    }

    public function retrySync($id)
    {
        $user = User::find($id);

        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            return;
        }

        // Asegurar que tenga un employee_no antes de enviar
        if (empty($user->employee_no)) {
            $user->employee_no = (string) $user->id;
            $user->save();
        }

        $isapiService = new IsapiService();
        $syncSuccess = $isapiService->addUser([
            'employeeNo' => $user->employee_no,
            'name' => $user->name,
        ]);

        if ($syncSuccess) {
            session()->flash('message', 'Sincronización exitosa para el usuario: ' . $user->name);
        } else {
            session()->flash('error', 'Falló el reintento de sincronización para: ' . $user->name);
        }
    }
}
