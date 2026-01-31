<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtensionesListado extends Model
{
    protected $table = 'extensiones_listado';
    
    // Deshabilitar timestamps (created_at, updated_at)
    public $timestamps = false;
    
    // Deshabilitar clave primaria ya que es compuesta (empresa, departamento, secuencia)
    // Eloquent no soporta claves primarias compuestas nativamente
    protected $primaryKey = null;
    
    // Deshabilitar auto-incremento
    public $incrementing = false;
    
    // Campos que forman la clave primaria compuesta
    protected $compositeKey = ['empresa', 'departamento', 'secuencia'];
    
    /**
     * Sobrescribir método getKeyName para evitar errores con clave primaria
     */
    public function getKeyName()
    {
        return $this->primaryKey;
    }
}