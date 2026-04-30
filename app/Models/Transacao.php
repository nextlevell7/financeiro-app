<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $table = 'transacaos';

    protected $fillable = [
        'data',
        'tipo',
        'origem',
        'categoria',
        'descricao',
        'valor',
    ];

    protected $casts = [
        'data' => 'date',
        'valor' => 'decimal:2',
    ];
}
