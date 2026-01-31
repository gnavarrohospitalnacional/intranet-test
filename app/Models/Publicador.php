<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Compania; 

class Publicador extends Model
{
    protected $table = 'hn_mob_publicador';
    
    // Definir la clave primaria correcta para Oracle
    protected $primaryKey = 'codigo';
    

    public function compania(): BelongsTo {       
        return $this->belongsTo(Compania::class, 'compania', 'codigo');  
    }

    public function tipo_evento(): BelongsTo {       
        return $this->belongsTo(TipoEvento::class, 'tipo_evento', 'codigo');  
    }
    
    public function subtipo_evento(): BelongsTo {       
        return $this->belongsTo(SubTipoEvento::class, 'subtipo_evento', 'codigo');  
    }

    public function target(): BelongsTo {       
        return $this->belongsTo(Target::class, 'codigo_target', 'codigo');  
    }
}
