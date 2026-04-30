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
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()]);

        $query = Transacao::where('user_id', Auth::id())
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

        $transacoes = $query
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->get();

        $totalReceitas = (clone $baseMes)->where('tipo', 'receita')->sum('valor');
        $totalDespesas = (clone $baseMes)->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        // Compatibilidade com views antigas que usam nomes simples
        $receitas = $totalReceitas;
        $despesas = $totalDespesas;

        $receitaApp = (clone $baseMes)
            ->where('tipo', 'receita')
            ->where('origem', 'app')
            ->sum('valor');

        $receitaGoverno = (clone $baseMes)
            ->where('tipo', 'receita')
            ->where('origem', 'governo')
            ->sum('valor');

        $custosCarro = (clone $baseMes)
            ->where('tipo', 'despesa')
            ->where(function ($q) {
                $q->where('origem', 'app')
                    ->orWhereIn('categoria', [
                        'Combustível',
                        'combustivel',
                        'Combustivel',
                        'Manutenção',
                        'manutencao',
                        'Manutencao',
                        'Seguro',
                        'Lavagem',
                        'Pneus',
                    ]);
            })
            ->sum('valor');

        $lucroApp = $receitaApp - $custosCarro;

        $mediaDiaria = $totalReceitas > 0
            ? $totalReceitas / max(1, $inicio->daysInMonth)
            : 0;

        $percentualCustoApp = $receitaApp > 0
            ? ($custosCarro / $receitaApp) * 100
            : 0;

        $percentualSobra = $totalReceitas > 0
            ? ($saldo / $totalReceitas) * 100
            : 0;

        $categorias = Transacao::where('user_id', Auth::id())
            ->whereNotNull('categoria')
            ->where('categoria', '<>', '')
            ->select('categoria')
            ->distinct()
            ->orderBy('categoria')
            ->pluck('categoria');

        $graficoLabels = [];
        $graficoReceitas = [];
        $graficoDespesas = [];

        for ($dia = 1; $dia <= $fim->day; $dia++) {
            $dataDia = $inicio->copy()->day($dia)->toDateString();
            $graficoLabels[] = str_pad((string) $dia, 2, '0', STR_PAD_LEFT);

            $graficoReceitas[] = (float) Transacao::where('user_id', Auth::id())
                ->whereDate('data', $dataDia)
                ->where('tipo', 'receita')
                ->sum('valor');

            $graficoDespesas[] = (float) Transacao::where('user_id', Auth::id())
                ->whereDate('data', $dataDia)
                ->where('tipo', 'despesa')
                ->sum('valor');
        }

        return view('index', compact(
            'transacoes',
            'mes',
            'tipo',
            'origem',
            'categoria',
            'categorias',
            'totalReceitas',
            'totalDespesas',
            'receitas',
            'despesas',
            'saldo',
            'receitaApp',
            'receitaGoverno',
            'custosCarro',
            'lucroApp',
            'mediaDiaria',
            'percentualCustoApp',
            'percentualSobra',
            'graficoLabels',
            'graficoReceitas',
            'graficoDespesas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'data' => ['required', 'date'],
            'tipo' => ['required', 'in:receita,despesa'],
            'origem' => ['nullable', 'string', 'max:255'],
            'categoria' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['origem'] = $validated['origem'] ?? 'pessoal';

        Transacao::create($validated);

        return redirect()->back()->with('success', 'Lançamento salvo com sucesso!');
    }

    public function destroy(Transacao $transacao)
    {
        if ((int) $transacao->user_id !== (int) Auth::id()) {
            abort(403);
        }

        $transacao->delete();

        return redirect()->back()->with('success', 'Lançamento excluído com sucesso!');
    }

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
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->orderByDesc('data')
            ->orderByDesc('id')
            ->get();

        return view('relatorio', compact('transacoes', 'mes'));
    }
}
