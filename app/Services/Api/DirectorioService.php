<?php

namespace App\Services\Api;

use App\Models\ExtensionesListado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DirectorioService
{
    protected $ldapConnection = null;
    protected $ldapBaseDn = "dc=HNACIONAL,dc=COM";
    protected $isConnected = false;
    public $users = [];
    /**
     * Query base para directorio
     */
    private function baseDirectorioQuery(array $selectFields = [])
    {
        $defaultFields = [
            'EXTENSIONES_LISTADO.empresa',
            'EXTENSIONES_EMPRESA.descripcion as company',
            'EXTENSIONES_LISTADO.departamento',
            'EXTENSIONES_DEPARTAMENTO.descripcion as department',
            'EXTENSIONES_LISTADO.secuencia',
            'EXTENSIONES_LISTADO.ubicacion_personal'
            
        ];
        $fields = !empty($selectFields) ? $selectFields : $defaultFields;
        
        return DB::table('EXTENSIONES_LISTADO')
            ->select($fields)
            ->selectRaw("EXTENSIONES_LISTADO.no_directo AS no_directo")
            ->selectRaw("null as position")
            ->selectRaw("null as cell_phone")
            ->selectRaw("null as email")
            ->selectRaw("EXTENSIONES_LISTADO.extension as extension")
            ->join('EXTENSIONES_DEPARTAMENTO', function($join) {
                $join->on('EXTENSIONES_LISTADO.empresa', '=', 'EXTENSIONES_DEPARTAMENTO.empresa')
                    ->on('EXTENSIONES_LISTADO.departamento', '=', 'EXTENSIONES_DEPARTAMENTO.codigo');
            })
            ->join('EXTENSIONES_EMPRESA', function($join) {
                $join->on('EXTENSIONES_LISTADO.empresa', '=', 'EXTENSIONES_EMPRESA.codigo');
            })
            ->where('EXTENSIONES_LISTADO.activo', 'S')
            ->where('EXTENSIONES_LISTADO.tipo', 'T');
    }

    /**
     * Aplicar filtros a la query de directorio
     */
    private function aplicarFiltros($query, array $filtros = [])
    {
        if (isset($filtros['empresa_id'])) {
            $query->where('EXTENSIONES_LISTADO.empresa', $filtros['empresa_id']);
        }

        if (isset($filtros['company'])) {
            $query->where('EXTENSIONES_EMPRESA.descripcion', $filtros['company']);
        }

        if (isset($filtros['department'])) {
            $query->where('EXTENSIONES_DEPARTAMENTO.descripcion', $filtros['department']);
        }

        if (isset($filtros['order_by'])) {
            $direction = $filtros['order_direction'] ?? 'ASC';
            $query->orderBy($filtros['order_by'], $direction);
        }

        if (isset($filtros['limit'])) {
            $query->limit($filtros['limit']);
        }

        return $query;
    }

    /**
     * Obtener últimos directorio
     */
    public function ultimosDirectorio(int $empresaId, int $perPage = 10)
    {
        $query = $this->baseDirectorioQuery();
        
        return $this->aplicarFiltros($query, [
            'empresa_id' => $empresaId,
            'order_by' => 'EXTENSIONES_LISTADO.ubicacion_personal',
            'order_direction' => 'ASC',
            // Eliminar el límite para permitir la paginación completa
        ])->paginate($perPage);
    }

    /**
     * Obtener todos los registros del directorio (sin paginar)
     */
    public function allDirectorio()
    {
        $query = $this->baseDirectorioQuery();
        
        $directorio = $this->aplicarFiltros($query, [
            'order_by' => 'EXTENSIONES_LISTADO.ubicacion_personal',
            'order_direction' => 'ASC'
        ])->get();
        return $directorio;
    }

    /**
     * Conectar a LDAP
     */
    private function connect()
    {
        try {
            // Si ya está conectado, retornar
            if ($this->isConnected && $this->ldapConnection) {
                return true;
            }
            
            // Verificar si extensión LDAP está disponible
            if (!function_exists('ldap_connect')) {
                throw new \Exception('Extensión LDAP no está instalada o habilitada. Ejecuta: sudo apt-get install php-ldap');
            }
            
            Log::info('Conectando a LDAP...');
            
            // Credenciales LDAP desde variables de entorno
            $ldapPassword = env('LDAP_PASSWORD');
            $ldapUsername = env('LDAP_USERNAME');
            $adServer = env('LDAP_SERVER');
            
            // Conectar al servidor LDAP
            $this->ldapConnection = ldap_connect($adServer);
            
            if (FALSE === $this->ldapConnection) {
                throw new \Exception('Unable to connect to the LDAP server');
            }
            
            // Configurar opciones
            ldap_set_option($this->ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->ldapConnection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($this->ldapConnection, LDAP_OPT_NETWORK_TIMEOUT, 30);
            ldap_set_option($this->ldapConnection, LDAP_OPT_TIMELIMIT, 30);
            
            // Autenticar
            Log::info('Autenticando en LDAP...');
            if (!@ldap_bind($this->ldapConnection, $ldapUsername, $ldapPassword)) {
                $error = ldap_error($this->ldapConnection);
                Log::error('LDAP bind failed: ' . $error);
                throw new \Exception('LDAP bind failed: ' . $error);
            }
            
            $this->isConnected = true;
            Log::info('Conexión LDAP exitosa');
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error en conexión LDAP: ' . $e->getMessage());
            $this->isConnected = false;
            $this->ldapConnection = null;
            throw $e;
        }
    }
    
    /**
     * Verificar y establecer conexión si es necesario
     */
    private function ensureConnection()
    {
        if (!$this->isConnected || !$this->ldapConnection) {
            $this->connect();
        }
    }
    
    /**
     * Obtener todos los usuarios de LDAP
     */
    public function getAllUsers()
    {
        try {
            $this->ensureConnection();
            
            Log::info('Buscando usuarios en LDAP...');
            
            // Filtro para obtener todos los usuarios
            $searchFilter = "(&(objectClass=user)(objectCategory=person))";
            
            // Campos a recuperar
            $attributes = [
                'samaccountname',
                'sn', 
                'givenname',
                'company',
                'department',
                'ipphone',
                'facsimiletelephonenumber',
                'mobile',
                'telephonenumber',
                'mail',
                'homephone',
                'title',
                'physicaldeliveryofficename',
                'displayname'
            ];
            
            // Realizar búsqueda
            $result = @ldap_search($this->ldapConnection, $this->ldapBaseDn, $searchFilter, $attributes);
            
            if (FALSE === $result) {
                $error = ldap_error($this->ldapConnection);
                Log::error('LDAP search failed: ' . $error);
                throw new \Exception('LDAP search failed: ' . $error);
            }
            
            // Obtener resultados
            $entries = ldap_get_entries($this->ldapConnection, $result);
            
            Log::info('Encontrados ' . $entries['count'] . ' usuarios en LDAP');
            
            if ($entries['count'] > 0) {
                for ($x = 0; $x < $entries['count']; $x++) {
                    // Solo incluir usuarios con información básica
                    if (isset($entries[$x]['samaccountname'][0])) {
                        $firstName = $entries[$x]['givenname'][0] ?? '';
                        $lastName = $entries[$x]['sn'][0] ?? '';
                        $fullName = trim($firstName . ' ' . $lastName);
                        
                        // Usar displayname si está disponible y fullName está vacío
                        if (empty($fullName) && isset($entries[$x]['displayname'][0])) {
                            $fullName = $entries[$x]['displayname'][0];
                        }
                        $extension = $entries[$x]['ipphone'][0] ?? '';
                        
                        $users[] = [
                            'username' => $entries[$x]['samaccountname'][0] ?? '',
                            'last_name' => $lastName,
                            'first_name' => $firstName,
                            'company' => $entries[$x]['company'][0] ?? '',
                            'department' => $entries[$x]['department'][0] ?? '',
                            'office_phone' => $entries[$x]['ipphone'][0] ?? '',
                            'office_fax' => $entries[$x]['facsimiletelephonenumber'][0] ?? '',
                            'cell_phone' => $entries[$x]['mobile'][0] ?? '',
                            'ddi' => $entries[$x]['telephonenumber'][0] ?? '',
                            'email' => $entries[$x]['mail'][0] ?? '',
                            'home_phone' => $entries[$x]['homephone'][0] ?? '',
                            'position' => $entries[$x]['title'][0] ?? '',
                            'office_location' => $entries[$x]['physicaldeliveryofficename'][0] ?? '',
                            // Campos para compatibilidad
                            'ubicacion_personal' => $fullName,
                            'extension' => $entries[$x]['ipphone'][0] ?? '',
                            'no_directo' => $entries[$x]['telephonenumber'][0] ?? '',
                            'descripcion' => $entries[$x]['department'][0] ?? '',
                            'activo' => 'S',
                            'tipo' => 'T',
                            'empresa' => $entries[$x]['company'][0] ?? '01',
                            'secuencia' => $x + 1,
                        ];
                    }
                }
            }
            $dataCollection = collect($users);
            return $users;
            
        } catch (\Exception $e) {
            Log::error('Error en getAllUsers: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Versión con caché para mejor performance
     */
    public function getAllUsersCached($cacheMinutes = 60)
    {
        $cacheKey = 'ldap_all_users_cache';
        
        return Cache::remember($cacheKey, $cacheMinutes * 60, function () {
            return $this->getAllUsers();
        });
    }
    
    /**
     * Obtener usuarios filtrados por empresa
     */
    public function getUsersByCompany($company = null)
    {
        $allUsers = $this->getAllUsersCached();
        
        if ($company) {
            return array_filter($allUsers, function($user) use ($company) {
                return !empty($user['company']) && stripos($user['company'], $company) !== false;
            });
        }
        
        return $allUsers;
    }
    
    /**
     * Buscar usuarios por nombre o departamento
     */
    public function searchUsers($searchTerm)
    {
        $allUsers = $this->getAllUsersCached();
        
        if (empty($searchTerm)) {
            return $allUsers;
        }
        
        $searchTerm = strtolower($searchTerm);
        
        return array_filter($allUsers, function($user) use ($searchTerm) {
            return str_contains(strtolower($user['ubicacion_personal'] ?? ''), $searchTerm) ||
                   str_contains(strtolower($user['department'] ?? ''), $searchTerm) ||
                   str_contains(strtolower($user['email'] ?? ''), $searchTerm) ||
                   str_contains(strtolower($user['company'] ?? ''), $searchTerm);
        });
    }
    
    /**
     * Ordenar usuarios
     */
    public function sortUsers($users, $field = 'ubicacion_personal', $direction = 'ASC')
    {
        if (empty($users)) {
            return [];
        }
        
        usort($users, function($a, $b) use ($field, $direction) {
            $valueA = strtolower($a[$field] ?? '');
            $valueB = strtolower($b[$field] ?? '');
            
            if ($direction === 'ASC') {
                return strcmp($valueA, $valueB);
            } else {
                return strcmp($valueB, $valueA);
            }
        });
        
        return $users;
    }
    
    /**
     * Paginar usuarios (simulación)
     */
    public function paginateUsers($users, $page = 1, $perPage = 20)
    {
        $total = count($users);
        $offset = ($page - 1) * $perPage;
        
        return [
            'data' => array_slice($users, $offset, $perPage),
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage),
        ];
    }
    
   
    /**
     * Prueba de conexión LDAP
     */
    public function testConnection()
    {
        try {
            $this->connect();
            
            if ($this->isConnected) {
                // Hacer una búsqueda simple para verificar
                $searchFilter = "(objectClass=*)";
                $result = @ldap_read($this->ldapConnection, $this->ldapBaseDn, $searchFilter, ['dn']);
                
                if ($result) {
                    return [
                        'success' => true,
                        'message' => 'Conexión LDAP exitosa',
                        'connection' => $this->ldapConnection ? 'Activa' : 'Inactiva',
                        'base_dn' => $this->ldapBaseDn
                    ];
                }
            }
            
            return [
                'success' => false,
                'message' => 'No se pudo verificar la conexión LDAP'
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    public function __destruct()
    {
        if ($this->ldapConnection && $this->isConnected) {
            try {
                @ldap_unbind($this->ldapConnection);
                $this->isConnected = false;
                $this->ldapConnection = null;
                Log::info('Conexión LDAP cerrada');
            } catch (\Exception $e) {
                // Ignorar errores en destructor
            }
        }
    }


}




