<?php

namespace App\Livewire\Pages\Phones;

use Livewire\Component;
use Livewire\WithPagination;
use App\Services\Api\DirectorioService;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class PhonesIntranet extends Component
{
    public $users = [];
    public $usersLDAP = [];
    public $search = '';
    public $originalUsers = []; // Almacenar los datos originales
    public $companies = [];
    public $selectedCompany = '';
    public $departments = [];
    public $selectedDepartment = '';
    public $page = 1; // Inicializar la propiedad $page
    use WithPagination;

    public function mount()
    {
        $directorioService = new DirectorioService();

        // Obtener todos los registros (sin paginar) para poder construir la paginación en Livewire
        $initial = $directorioService->allDirectorio();

        if ($initial instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $items = collect($initial->items())->map(function ($u) {
                return is_object($u) ? $u : (object) $u;
            });
        } else {
            $items = collect($initial)->map(function ($user) {
                return is_object($user) ? $user : (object) $user;
            });
        }

        // Obtener datos LDAP y mapear
        $usersArray = $directorioService->getAllUsers();
        $this->usersLDAP = collect($usersArray)->map(function($user) {
            return (object) $user;
        });

        $usersLDAPIndexado = $this->usersLDAP->keyBy('ubicacion_personal');
        //mapeo de datos ldap a los usuarios
        $mapped = $items->map(function ($user) use ($usersLDAPIndexado) {
            if ($usersLDAPIndexado->has($user->ubicacion_personal)) {
                $ldapUser = $usersLDAPIndexado->get($user->ubicacion_personal);
                $user->cell_phone = $ldapUser->cell_phone ?? null;
                $user->email = $ldapUser->email ?? null;
                $user->position = $ldapUser->position ?? null;
                $user->department = $ldapUser->department ?? $user->department;
            }

            return $user;
        })->values();

        // Guardar los datos originales como array serializable para Livewire
        $this->originalUsers = $mapped->map(function($u){ return is_object($u) ? (array) $u : $u; })->values()->toArray();

        // Preparar lista de compañías únicas para el select (strings ordenados)
        $this->companies = $mapped->pluck('company')
            ->filter(function($c){ return !empty($c); })
            ->unique()
            ->values()
            ->sort()
            ->values()
            ->toArray();

        // Inicialmente no hay departamentos cargados
        $this->departments = [];
    }

    public function render()
    {
        $filteredUsers = collect($this->originalUsers); // Usar los datos originales para filtrar

        if (!empty($this->search)) {
            $searchTerm = strtolower($this->search);

            $filteredUsers = $filteredUsers->filter(function ($user) use ($searchTerm) {
                $ubicacion = '';
                $position = '';
                $department = '';
                $email = '';
                $company = '';

                if (is_array($user)) {
                    $ubicacion = $user['ubicacion_personal'] ?? '';
                    $position = $user['position'] ?? '';
                    $department = $user['department'] ?? '';
                    $email = $user['email'] ?? '';
                    $company = $user['company'] ?? '';
                } elseif (is_object($user)) {
                    $ubicacion = $user->ubicacion_personal ?? '';
                    $position = $user->position ?? '';
                    $department = $user->department ?? '';
                    $email = $user->email ?? '';
                    $company = $user->company ?? '';
                }

                $hay = str_contains(strtolower((string) $ubicacion), $searchTerm) ||
                       str_contains(strtolower((string) $position), $searchTerm) ||
                       str_contains(strtolower((string) $department), $searchTerm) ||
                       str_contains(strtolower((string) $email), $searchTerm) ||
                       str_contains(strtolower((string) $company), $searchTerm);

                return $hay;
            });
        }

        $filtered = $filteredUsers->values(); // Colección completa filtrada

        // Filtrar por compañía seleccionada si aplica (comparación normalizada)
        if (!empty($this->selectedCompany)) {
            $selectedNorm = strtolower(trim($this->selectedCompany));
            
            $filtered = $filtered->filter(function($u) use ($selectedNorm) {
                $companyValue = '';
                if (is_array($u)) {
                    $companyValue = $u['company'] ?? '';
                } elseif (is_object($u)) {
                    $companyValue = $u->company ?? '';
                }

                return strtolower(trim((string) $companyValue)) === $selectedNorm;
            })->values();
        }

        // Filtrar por departamento seleccionado si aplica (comparación normalizada)
        if (!empty($this->selectedDepartment)) {
            $selectedDept = strtolower(trim($this->selectedDepartment));
            $filtered = $filtered->filter(function($u) use ($selectedDept) {
                $deptValue = '';
                if (is_array($u)) {
                    $deptValue = $u['department'] ?? '';
                } elseif (is_object($u)) {
                    $deptValue = $u->department ?? '';
                }

                return strtolower(trim((string) $deptValue)) === $selectedDept;
            })->values();
        }

        // Implementar paginación con LengthAwarePaginator
        $currentPage = $this->page ?? request()->input('page', 1);
        $perPage = 12;
        $itemsForPage = $filtered->forPage($currentPage, $perPage)->values();

        // Convertir items a objetos para la vista (permitir acceso ->ubicacion_personal)
        $itemsForDisplay = $itemsForPage->map(function($u) {
            return is_object($u) ? $u : (object) $u;
        })->values();

        $paginatedUsers = new LengthAwarePaginator(
            $itemsForDisplay,
            $filtered->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Asegurar que la colección interna del paginador sea una Collection de objetos
        $paginatedUsers->setCollection(collect($itemsForDisplay));

        // Guardar solo datos serializables en la propiedad pública para Livewire
        $this->users = $itemsForPage->map(function ($u) {
            return is_object($u) ? (array) $u : (array) $u;
        })->values()->toArray();

        return view('livewire.pages.phones.phones-intranet', [
            'paginatedUsers' => $paginatedUsers // Pasar el paginador a la vista con nombre distinto
        ]);
    }

    // Método para cambiar la página desde la vista Livewire
    public function setPage($page)
    {
        $this->page = (int) $page;
    }

    // Cuando la búsqueda cambie, resetear la página a 1
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Cuando la compañía seleccionada cambie, resetear la página
    public function updatedSelectedCompany()
    {
        $this->resetPage();
    }

    // Método explícito para aplicar compañía desde la vista (wire:change)
    public function applyCompany($company)
    {
        $this->selectedCompany = $company;
        $this->resetPage();

        // Limpiar la selección de departamento siempre que cambie la compañía
        $this->selectedDepartment = '';

        // Cargar departamentos asociados a la compañía seleccionada
        if (!empty($company)) {
            $companyNorm = strtolower(trim($company));
            $all = collect($this->originalUsers);
            $deps = $all->filter(function($u) use ($companyNorm) {
                $val = is_array($u) ? ($u['company'] ?? '') : ($u->company ?? '');
                return strtolower(trim((string)$val)) === $companyNorm;
            })->map(function($u) {
                return is_array($u) ? ($u['department'] ?? '') : ($u->department ?? '');
            })->filter(function($d){ return !empty($d); })
              ->unique()
              ->values()
              ->sort()
              ->values()
              ->toArray();

            $this->departments = $deps;
        } else {
            // si se limpia compañía, limpiar departamentos y selección
            $this->departments = [];
            $this->selectedDepartment = '';
        }
    }

    // Aplicar departamento seleccionado (desde wire:change)
    public function applyDepartment($department)
    {
        $this->selectedDepartment = $department;
        $this->resetPage();
    }
}