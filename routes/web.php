<?php

use App\Http\Controllers\TransacaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TransacaoController::class, 'index'])->name('financeiro.index');
Route::post('/transacoes', [TransacaoController::class, 'store'])->name('transacoes.store');
Route::delete('/transacoes/{transacao}', [TransacaoController::class, 'destroy'])->name('transacoes.destroy');
Route::get('/relatorio', [TransacaoController::class, 'relatorio'])->name('financeiro.relatorio');
