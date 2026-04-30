<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transacao extends Model
{
    protected $table = 'transacaos';

    protected $fillable = [
        'user_id',
        'data',
        'tipo',
        'origem',
        'categoria',
        'descricao',
        'valor'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}