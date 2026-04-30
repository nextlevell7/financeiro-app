<?php

use App\Http\Controllers\TransacaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('financeiro.index')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [TransacaoController::class, 'index'])->name('dashboard');
    Route::get('/financeiro', [TransacaoController::class, 'index'])->name('financeiro.index');
    Route::post('/transacoes', [TransacaoController::class, 'store'])->name('transacoes.store');
    Route::delete('/transacoes/{transacao}', [TransacaoController::class, 'destroy'])->name('transacoes.destroy');
    Route::get('/relatorio', [TransacaoController::class, 'relatorio'])->name('financeiro.relatorio');
});

require __DIR__.'/auth.php';
