<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransacaoController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('financeiro.index')
        : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('financeiro.index');
    })->name('dashboard');

    Route::get('/financeiro', [TransacaoController::class, 'index'])->name('financeiro.index');
    Route::post('/transacoes', [TransacaoController::class, 'store'])->name('transacoes.store');
    Route::delete('/transacoes/{transacao}', [TransacaoController::class, 'destroy'])->name('transacoes.destroy');
    Route::get('/relatorio', [TransacaoController::class, 'relatorio'])->name('financeiro.relatorio');

    Route::post('/logout-financeiro', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('financeiro.logout');
});

require __DIR__.'/auth.php';
