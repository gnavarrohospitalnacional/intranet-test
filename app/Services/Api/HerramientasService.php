<?php

namespace App\Services\Api;

use App\Models\IconoEquipo;
use Illuminate\Support\Facades\DB;

class HerramientasService
{
    public function index()
    {
        return IconoEquipo::query()
            ->select('nombre_equipo')
            ->orderBy('nombre_equipo')
            ->get();
    }

     /**
     * Herramientas por equipo
     */
    public function herramientasBase(string $nombreEquipo, int $rolId = null, int $limit = 5, bool $personal = true)
    {
        $nombreEquipoUpper = strtoupper($nombreEquipo);
        
        // Optimización: reutilizar condiciones comunes
        $commonConditions = function ($query) {
            $query->whereRaw("(appdet.fecha_fin is null or appdet.fecha_fin < sysdate)");
            $query->whereRaw("(app.activo = 'S')");
        };

        $defaultFields = [
            'amb.cd_unificador_ambiente as CodigoAmbiente',
            'amb.ds_unificador_ambiente as NombreAmbiente',
            'amb.orden as orden_ambiente',
            'app.icono_url',
            'appdet.app_url',
            'app.nombre',
            'app.descripcion as r_aspecto',
            'nvl(app.orden,99) as orden_app',
            'amb.browser',
            'app.descripcion_corta',
            'app.icono_intranet'
        ];
        $defaultFields  = implode(', ', $defaultFields);
        
        // PRIMERA PARTE - si el nombre del equipo está guardado y tiene asignado aplicaciones y ambientes personalizados
        $query1 = DB::table('inf_icono_equipo as e')
            ->selectRaw($defaultFields)
            ->join('inf_icono_equipoamb as eamb', 'eamb.nombre_equipo', '=', 'e.nombre_equipo')
            ->join('inf_desktop_ambientes as amb', 'amb.cd_desktop_amb', '=', 'eamb.cd_desktop_amb')
            ->join('inf_icono_equipoapp as eapp', 'eapp.nombre_equipo', '=', 'e.nombre_equipo')
            ->join('inf_desktop_app as app', 'app.cd_desktop_app', '=', 'eapp.cd_desktop_app')
            ->join('inf_desktop_appdet as appdet', function ($join) {
                $join->on('appdet.cd_desktop_app', '=', 'app.cd_desktop_app')
                    ->on('appdet.cd_desktop_amb', '=', 'amb.cd_desktop_amb');
            })
            ->whereRaw("upper(e.nombre_equipo) = ?", [$nombreEquipoUpper])
            ->tap($commonConditions);
        
        // SEGUNDA PARTE - si el nombre del equipo está guardado y tiene un rol personalizado
        $query2 = DB::table('inf_icono_equipo as e')
            ->selectRaw($defaultFields)
            ->join('inf_desktop_rol as r', 'e.cd_desktop_rol', '=', 'r.cd_desktop_rol')
            ->join('inf_desktop_rolamb as ramb', 'ramb.cd_desktop_rol', '=', 'e.cd_desktop_rol')
            ->join('inf_desktop_ambientes as amb', 'amb.cd_desktop_amb', '=', 'ramb.cd_desktop_amb')
            ->join('inf_desktop_rolapp as rapp', 'rapp.cd_desktop_rol', '=', 'e.cd_desktop_rol')
            ->join('inf_desktop_app as app', 'app.cd_desktop_app', '=', 'rapp.cd_desktop_app')
            ->join('inf_desktop_appdet as appdet', function ($join) {
                $join->on('appdet.cd_desktop_app', '=', 'app.cd_desktop_app')
                    ->on('appdet.cd_desktop_amb', '=', 'amb.cd_desktop_amb');
            })
            ->where('r.activo', 'S')
            ->where('r.cd_desktop_rol', $rolId)
            ->whereRaw("upper(e.nombre_equipo) = ?", [$nombreEquipoUpper])
            ->tap($commonConditions);
        
        // TERCERA PARTE - si el nombre del equipo no está guardado
        $query3 = DB::table('inf_desktop_rol as r')
            ->selectRaw($defaultFields)
            ->join('inf_desktop_rolamb as ramb', 'ramb.cd_desktop_rol', '=', 'r.cd_desktop_rol')
            ->join('inf_desktop_ambientes as amb', 'amb.cd_desktop_amb', '=', 'ramb.cd_desktop_amb')
            ->join('inf_desktop_rolapp as rapp', 'rapp.cd_desktop_rol', '=', 'r.cd_desktop_rol')
            ->join('inf_desktop_app as app', 'app.cd_desktop_app', '=', 'rapp.cd_desktop_app')
            ->join('inf_desktop_appdet as appdet', function ($join) {
                $join->on('appdet.cd_desktop_app', '=', 'app.cd_desktop_app')
                    ->on('appdet.cd_desktop_amb', '=', 'amb.cd_desktop_amb');
            })
            ->where('r.activo', 'S')
            ->where('r.cd_desktop_rol', $rolId)
            ->tap($commonConditions);
        
        // Crear un subquery para aplicar el límite correctamente
        // Excluir PRIMERA PARTE cuando $personal sea false
        if ($personal) {
            $unionQuery = $query1->unionAll($query2)->unionAll($query3);
        } else {
            $unionQuery = $query3;
        }
    
        $result = DB::select("
            SELECT * FROM (
                {$unionQuery->toSql()}
                ORDER BY orden_ambiente, orden_app
            ) WHERE ROWNUM <= ?
        ", array_merge($unionQuery->getBindings(), [$limit]));
        
        return $result;
    }

    // Rol por defecto "APLICACIONES INTRANET"
    public function herramientasPorEquipo(string $nombreEquipo,$limit = 99){
        return $this->herramientasBase($nombreEquipo, 5, $limit, true);
    }

    // Rol por defecto "APLICACIONES EXTERNAS"
    public function appexternasPorEquipo(string $nombreEquipo,$limit = 99){
        return $this->herramientasBase($nombreEquipo, 6, $limit, false);
    }

    public function HostnameNTLM()
    {
        // This a copy taken 2008-08-21 from http://siphon9.net/loune/f/ntlm.php.txt to make sure the code is not lost.
        // For more information see:
        // http://blogs.msdn.com/cellfish/archive/2008/08/26/getting-the-logged-on-windows-user-in-your-apache-server.aspx

        // NTLM specs http://davenport.sourceforge.net/ntlm.html

        $headers = apache_request_headers();

        if (!isset($headers['Authorization'])){
            header('HTTP/1.1 401 Unauthorized');
            header('WWW-Authenticate: NTLM');
        exit;
        }

        $auth = $headers['Authorization'];

        if (substr($auth,0,5) == 'NTLM ') {
        $msg = base64_decode(substr($auth, 5));
        if (substr($msg, 0, 8) != "NTLMSSP\x00")
                die('error header not recognised');

        if ($msg[8] == "\x01") {
                $msg2 = "NTLMSSP\x00\x02"."\x00\x00\x00\x00". // target name len/alloc
                "\x00\x00\x00\x00". // target name offset
                "\x01\x02\x81\x01". // flags
                    "\x00\x00\x00\x00\x00\x00\x00\x00". // challenge
                    "\x00\x00\x00\x00\x00\x00\x00\x00". // context
                "\x00\x00\x00\x00\x30\x00\x00\x00"; // target info len/alloc/offset

            header('HTTP/1.1 401 Unauthorized');
                header('WWW-Authenticate: NTLM '.trim(base64_encode($msg2)));
            exit;
        }
        else if ($msg[8] == "\x03") {

            $user = $this->get_msg_str($msg, 36);
            $domain = $this->get_msg_str($msg, 28);
            $workstation = $this->get_msg_str($msg, 44);
            $datos = [];

            $datos['data'] = array(
                array(
                    "hostname" => $workstation  // Si también quieres incluir el hostname aquí
                )
            );

            return $datos;

            }
        }
    }

    public function get_msg_str($msg, $start, $unicode = true) {
    $len = (ord($msg[$start+1]) * 256) + ord($msg[$start]);
    $off = (ord($msg[$start+5]) * 256) + ord($msg[$start+4]);
    if ($unicode)
        return str_replace("\0", '', substr($msg, $off, $len));
    else
            return substr($msg, $off, $len);
    }

}
