<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transacao;
use Carbon\Carbon;

class TransacaoController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->mes ?? now()->format('Y-m');

        $inicio = Carbon::parse($mes . '-01')->startOfMonth();
        $fim = Carbon::parse($mes . '-01')->endOfMonth();

        // 🔐 FILTRO POR USUÁRIO
        $query = Transacao::where('user_id', auth()->id())
            ->whereBetween('data', [$inicio, $fim]);

        if ($request->tipo && $request->tipo != 'todos') {
            $query->where('tipo', $request->tipo);
        }

        if ($request->origem && $request->origem != 'todas') {
            $query->where('origem', $request->origem);
        }

        if ($request->categoria && $request->categoria != 'todas') {
            $query->where('categoria', $request->categoria);
        }

        $transacoes = $query->orderByDesc('data')->orderByDesc('id')->get();

        // 📊 cálculos
        $totalReceitas = $transacoes->where('tipo', 'receita')->sum('valor');
        $totalDespesas = $transacoes->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        // 📈 gráfico
        $graficoLabels = [];
        $graficoReceitas = [];
        $graficoDespesas = [];

        foreach (range(1, $fim->day) as $dia) {
            $data = $inicio->copy()->day($dia)->format('Y-m-d');

            $graficoLabels[] = $dia;

            $graficoReceitas[] = $transacoes
                ->where('tipo', 'receita')
                ->where('data', $data)
                ->sum('valor');

            $graficoDespesas[] = $transacoes
                ->where('tipo', 'despesa')
                ->where('data', $data)
                ->sum('valor');
        }

        return view('index', compact(
            'transacoes',
            'mes',
            'totalReceitas',
            'totalDespesas',
            'saldo',
            'graficoLabels',
            'graficoReceitas',
            'graficoDespesas'
        ));
    }

    public function store(Request $request)
    {
        Transacao::create([
            'user_id' => auth()->id(), // 🔐 IMPORTANTE
            'data' => $request->data,
            'tipo' => $request->tipo,
            'origem' => $request->origem,
            'categoria' => $request->categoria,
            'descricao' => $request->descricao,
            'valor' => $request->valor
        ]);

        return redirect()->back();
    }

    public function destroy(Transacao $transacao)
    {
        // 🔐 segurança extra
        if ($transacao->user_id != auth()->id()) {
            abort(403);
        }

        $transacao->delete();

        return redirect()->back();
    }
}