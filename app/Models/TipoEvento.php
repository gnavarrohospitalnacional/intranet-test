<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Compania; 

class TipoEvento extends Model
{
    //
    protected $table = 'hn_mob_tipo_evento';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    
    public function compania(): BelongsTo {       
        return $this->belongsTo(Compania::class, 'compania', 'codigo');  
    }

    public function publicadores()
    {
        return $this->hasMany(Publicador::class, 'tipo_evento', 'codigo');
    }
}
