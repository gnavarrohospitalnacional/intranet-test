<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Compania; 

class InfArea extends Model
{
    //
    protected $table = 'inf_area';

    public function compania(): BelongsTo {       
        return $this->belongsTo(Compania::class, 'compania', 'codigo');  
    }

}