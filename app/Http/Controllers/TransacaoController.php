<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransacaoController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes', now()->format('Y-m'));
        $tipo = $request->get('tipo', 'todos');
        $origem = $request->get('origem', 'todas');
        $categoria = $request->get('categoria', 'todas');

        try {
            $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        } catch (\Throwable $e) {
            $mes = now()->format('Y-m');
            $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        }

        $fim = $inicio->copy()->endOfMonth();

        $baseMes = Transacao::where('user_id', Auth::id())
            ->whereBetween('data', [$inicio, $fim]);

        $query = clone $baseMes;

        if ($tipo !== 'todos') {
            $query->where('tipo', $tipo);
        }

        if ($origem !== 'todas') {
            $query->where('origem', $origem);
        }

        if ($categoria !== 'todas') {
            $query->where('categoria', $categoria);
        }

        $transacoes = $query->orderByDesc('data')->get();

        $totalReceitas = (clone $baseMes)->where('tipo', 'receita')->sum('valor');
        $totalDespesas = (clone $baseMes)->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        return view('index', compact(
            'transacoes',
            'mes',
            'tipo',
            'origem',
            'categoria',
            'totalReceitas',
            'totalDespesas',
            'saldo'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'data' => 'required|date',
            'tipo' => 'required|in:receita,despesa',
            'origem' => 'nullable|string',
            'categoria' => 'required|string',
            'descricao' => 'nullable|string',
            'valor' => 'required|numeric',
        ]);

        $data['user_id'] = Auth::id();

        Transacao::create($data);

        return back();
    }

    public function destroy(Transacao $transacao)
    {
        if ($transacao->user_id !== Auth::id()) {
            abort(403);
        }

        $transacao->delete();

        return back();
    }

    // 🔥 RELATÓRIO CORRIGIDO
    public function relatorio(Request $request)
    {
        $mes = $request->get('mes', now()->format('Y-m'));

        try {
            $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        } catch (\Throwable $e) {
            $mes = now()->format('Y-m');
            $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        }

        $fim = $inicio->copy()->endOfMonth();

        $transacoes = Transacao::where('user_id', Auth::id())
            ->whereBetween('data', [$inicio, $fim])
            ->orderByDesc('data')
            ->get();

        $totalReceitas = $transacoes->where('tipo', 'receita')->sum('valor');
        $totalDespesas = $transacoes->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        return view('relatorio', compact(
            'transacoes',
            'mes',
            'totalReceitas',
            'totalDespesas',
            'saldo'
        ));
    }
}