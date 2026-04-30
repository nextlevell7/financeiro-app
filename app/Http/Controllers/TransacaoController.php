<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransacaoController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes', now()->format('Y-m'));
        $tipo = $request->get('tipo', 'todos');
        $origem = $request->get('origem', 'todas');
        $categoria = $request->get('categoria', 'todas');

        $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $fim = Carbon::createFromFormat('Y-m', $mes)->endOfMonth();

        $query = Transacao::query()
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()]);

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

        $baseMes = Transacao::query()
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()]);

        $totalReceitas = (clone $baseMes)->where('tipo', 'receita')->sum('valor');
        $totalDespesas = (clone $baseMes)->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        $receitaApp = (clone $baseMes)->where('tipo', 'receita')->where('origem', 'app')->sum('valor');
        $receitaGoverno = (clone $baseMes)->where('tipo', 'receita')->where('origem', 'governo')->sum('valor');
        $custosCarro = (clone $baseMes)->where('tipo', 'despesa')->where('origem', 'app')->sum('valor');
        $lucroApp = $receitaApp - $custosCarro;

        $mediaDiaria = $totalReceitas > 0 ? $totalReceitas / $inicio->daysInMonth : 0;
        $percentualCustoApp = $receitaApp > 0 ? ($custosCarro / $receitaApp) * 100 : 0;
        $percentualSobra = $totalReceitas > 0 ? ($saldo / $totalReceitas) * 100 : 0;

        $categorias = Transacao::query()
            ->select('categoria')
            ->whereNotNull('categoria')
            ->where('categoria', '!=', '')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $nomesOrigem = [
            'app' => 'App',
            'governo' => 'Governo',
            'pessoal' => 'Pessoal',
        ];

        return view('index', compact(
            'transacoes',
            'mes',
            'tipo',
            'origem',
            'categoria',
            'categorias',
            'nomesOrigem',
            'totalReceitas',
            'totalDespesas',
            'saldo',
            'receitaApp',
            'receitaGoverno',
            'custosCarro',
            'lucroApp',
            'mediaDiaria',
            'percentualCustoApp',
            'percentualSobra'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => 'required|date',
            'tipo' => 'required|in:receita,despesa',
            'origem' => 'required|string|max:50',
            'categoria' => 'nullable|string|max:100',
            'descricao' => 'nullable|string|max:255',
            'valor' => 'required|numeric|min:0',
        ]);

        Transacao::create($validated);

        return redirect()->route('financeiro.index', ['mes' => Carbon::parse($validated['data'])->format('Y-m')])
            ->with('success', 'Lançamento salvo com sucesso!');
    }

    public function destroy(Transacao $transacao)
    {
        $mes = Carbon::parse($transacao->data)->format('Y-m');
        $transacao->delete();

        return redirect()->route('financeiro.index', ['mes' => $mes])
            ->with('success', 'Lançamento excluído com sucesso!');
    }

    public function relatorio(Request $request)
    {
        $mes = $request->get('mes', now()->format('Y-m'));

        $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $fim = Carbon::createFromFormat('Y-m', $mes)->endOfMonth();

        $transacoes = Transacao::whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->get();

        $totalReceitas = $transacoes->where('tipo', 'receita')->sum('valor');
        $totalDespesas = $transacoes->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        return view('relatorio', compact('transacoes', 'mes', 'totalReceitas', 'totalDespesas', 'saldo'));
    }
}
