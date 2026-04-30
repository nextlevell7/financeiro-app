<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransacaoController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/financeiro', [TransacaoController::class, 'index'])->name('financeiro.index');

    Route::post('/transacoes', [TransacaoController::class, 'store'])->name('transacoes.store');

    Route::delete('/transacoes/{transacao}', [TransacaoController::class, 'destroy'])->name('transacoes.destroy');

});

require __DIR__.'/auth.php';