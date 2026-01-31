<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Compania; 

class SubTipoEvento extends Model
{
    //
    protected $table = 'hn_mob_subtipo_evento';

    public function compania(): BelongsTo {       
        return $this->belongsTo(Compania::class, 'compania', 'codigo');  
    }

    public function tipo_evento(): BelongsTo {       
        return $this->belongsTo(TipoEvento::class, 'tipo_evento', 'codigo');  
    }
}
