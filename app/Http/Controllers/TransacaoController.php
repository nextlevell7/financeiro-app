<?php

namespace App\Http\Controllers;

use App\Models\Transacao;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransacaoController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes', now()->format('Y-m'));
        $tipo = $request->get('tipo', 'todos');
        $origem = $request->get('origem', 'todos');
        $categoria = $request->get('categoria', 'todos');

        $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $fim = Carbon::createFromFormat('Y-m', $mes)->endOfMonth();

        $query = Transacao::query()->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()]);

        if ($tipo !== 'todos') {
            $query->where('tipo', $tipo);
        }

        if ($origem !== 'todos') {
            $query->where('origem', $origem);
        }

        if ($categoria !== 'todos') {
            $query->where('categoria', $categoria);
        }

        $transacoes = (clone $query)->orderByDesc('data')->orderByDesc('id')->get();

        $baseMes = Transacao::whereBetween('data', [$inicio->toDateString(), $fim->toDateString()]);

        $totalReceitas = (clone $baseMes)->where('tipo', 'receita')->sum('valor');
        $totalDespesas = (clone $baseMes)->where('tipo', 'despesa')->sum('valor');
        $saldo = $totalReceitas - $totalDespesas;

        $receitaApp = (clone $baseMes)->where('tipo', 'receita')->where('origem', 'app')->sum('valor');
        $receitaGoverno = (clone $baseMes)->where('tipo', 'receita')->where('origem', 'governo')->sum('valor');

        $categoriasCarro = ['combustivel', 'manutencao', 'seguro', 'financiamento', 'lavagem', 'estacionamento', 'outros_carro'];
        $custosCarro = (clone $baseMes)->where('tipo', 'despesa')->whereIn('categoria', $categoriasCarro)->sum('valor');
        $lucroApp = $receitaApp - $custosCarro;

        $percentualCustoApp = $receitaApp > 0 ? ($custosCarro / $receitaApp) * 100 : 0;
        $percentualSobra = $totalReceitas > 0 ? ($saldo / $totalReceitas) * 100 : 0;
        $mediaDiaria = $inicio->day > 0 ? $saldo / max(1, now()->format('Y-m') === $mes ? now()->day : $inicio->daysInMonth) : 0;

        $grafico = Transacao::selectRaw("strftime('%d', data) as dia")
            ->selectRaw("SUM(CASE WHEN tipo = 'receita' THEN valor ELSE 0 END) as receitas")
            ->selectRaw("SUM(CASE WHEN tipo = 'despesa' THEN valor ELSE 0 END) as despesas")
            ->whereBetween('data', [$inicio->toDateString(), $fim->toDateString()])
            ->groupBy(DB::raw("strftime('%d', data)"))
            ->orderBy(DB::raw("strftime('%d', data)"))
            ->get();

        $diasNoMes = $inicio->daysInMonth;
        $labels = [];
        $receitasGrafico = [];
        $despesasGrafico = [];

        for ($dia = 1; $dia <= $diasNoMes; $dia++) {
            $diaFormatado = str_pad((string) $dia, 2, '0', STR_PAD_LEFT);
            $item = $grafico->firstWhere('dia', $diaFormatado);

            $labels[] = $diaFormatado;
            $receitasGrafico[] = $item ? (float) $item->receitas : 0;
            $despesasGrafico[] = $item ? (float) $item->despesas : 0;
        }

        $categorias = [
            'salario' => 'Salário',
            'corrida_app' => 'Corrida App',
            'extra' => 'Extra',
            'combustivel' => 'Combustível',
            'manutencao' => 'Manutenção',
            'seguro' => 'Seguro',
            'financiamento' => 'Financiamento',
            'lavagem' => 'Lavagem',
            'estacionamento' => 'Estacionamento',
            'mercado' => 'Mercado',
            'alimentacao' => 'Alimentação',
            'casa' => 'Casa',
            'saude' => 'Saúde',
            'lazer' => 'Lazer',
            'outros_carro' => 'Outros do carro',
            'outros' => 'Outros',
        ];

        return view('index', compact(
            'mes', 'tipo', 'origem', 'categoria', 'categorias', 'transacoes',
            'totalReceitas', 'totalDespesas', 'saldo', 'receitaApp', 'receitaGoverno',
            'custosCarro', 'lucroApp', 'percentualCustoApp', 'percentualSobra',
            'mediaDiaria', 'labels', 'receitasGrafico', 'despesasGrafico'
        ));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'data' => ['required', 'date'],
            'tipo' => ['required', 'in:receita,despesa'],
            'origem' => ['required', 'in:app,governo,pessoal,outros'],
            'categoria' => ['required', 'string', 'max:100'],
            'descricao' => ['nullable', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'min:0.01'],
        ]);

        Transacao::create($dados);

        return redirect()->route('financeiro.index', ['mes' => Carbon::parse($dados['data'])->format('Y-m')])
            ->with('success', 'Lançamento salvo com sucesso.');
    }

    public function destroy(Transacao $transacao)
    {
        $mes = Carbon::parse($transacao->data)->format('Y-m');
        $transacao->delete();

        return redirect()->route('financeiro.index', ['mes' => $mes])->with('success', 'Lançamento excluído.');
    }

    public function relatorio(Request $request)
    {
        return redirect()->route('financeiro.index', $request->query());
    }
}
