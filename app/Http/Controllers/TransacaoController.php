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
        $userId = auth()->id();

        $mes = $request->mes ?? now()->format('Y-m');
        $tipo = $request->tipo ?? 'todos';
        $origem = $request->origem ?? 'todas';
        $categoria = $request->categoria ?? 'todas';

        $inicio = Carbon::parse($mes . '-01')->startOfMonth();
        $fim = Carbon::parse($mes . '-01')->endOfMonth();

        $query = Transacao::where('user_id', $userId)
            ->whereBetween('data', [$inicio, $fim]);

        if ($tipo !== 'todos') {
            $query->where('tipo', $tipo);
        }

        if ($origem !== 'todas') {
            $query->where('origem', $origem);
        }

        if ($categoria !== 'todas') {
            $query->where('categoria', $categoria);
        }

        $transacoes = $query->orderByDesc('data')->orderByDesc('id')->get();

        $totalReceitas = (clone $query)->where('tipo', 'receita')->sum('valor');
        $totalDespesas = (clone $query)->where('tipo', 'despesa')->sum('valor');

        $saldo = $totalReceitas - $totalDespesas;

        // ✅ CORREÇÃO IMPORTANTE (agora usa MES também)
        $categorias = Transacao::where('user_id', $userId)
            ->whereBetween('data', [$inicio, $fim])
            ->select('categoria')
            ->distinct()
            ->pluck('categoria');

        return view('index', compact(
            'transacoes',
            'totalReceitas',
            'totalDespesas',
            'saldo',
            'mes',
            'tipo',
            'origem',
            'categoria',
            'categorias'
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

    // ✅ RELATÓRIO DEFINITIVO (SEM ERRO 500)
    public function relatorio(Request $request)
    {
        $userId = Auth::id();

        $mes = $request->get('mes', now()->format('Y-m'));

        try {
            $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        } catch (\Throwable $e) {
            $mes = now()->format('Y-m');
            $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        }

        $fim = $inicio->copy()->endOfMonth();

        $transacoes = Transacao::where('user_id', $userId)
            ->whereBetween('data', [$inicio, $fim])
            ->orderByDesc('data')
            ->get();

        $totalReceitas = $transacoes->where('tipo', 'receita')->sum('valor');
        $totalDespesas = $transacoes->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        // ✅ ESSENCIAL pra evitar erro na view (gráfico ou categorias)
        $categorias = $transacoes->pluck('categoria')->unique();

        return view('relatorio', compact(
            'transacoes',
            'mes',
            'totalReceitas',
            'totalDespesas',
            'saldo',
            'categorias'
        ));
    }
}