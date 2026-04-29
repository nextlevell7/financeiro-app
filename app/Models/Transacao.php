<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $fillable = [
        'data',
        'tipo',
        'origem',
        'categoria',
        'descricao',
        'valor',
    ];
}
