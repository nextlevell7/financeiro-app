<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-100 text-slate-950">
@php
    function dinheiro($valor) {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }

    $nomesOrigem = [
        'app' => 'App',
        'governo' => 'Governo',
        'pessoal' => 'Pessoal',
    ];
@endphp

<div class="min-h-screen p-5 lg:p-10">
    <div class="max-w-7xl mx-auto">
        <div class="flex items-center justify-between gap-4 mb-8">
            <div>
                <p class="text-blue-700 font-black text-sm">Sistema pessoal</p>
                <h1 class="text-4xl lg:text-5xl font-black leading-none mt-2">💰 Controle<br>Financeiro</h1>
                <p class="text-slate-600 mt-4 text-lg">Ganhos, gastos, saldo e lucro real como motorista de app.</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('financeiro.relatorio', request()->query()) }}" class="bg-slate-950 text-white px-5 py-3 rounded-xl font-bold hover:bg-slate-800">Relatório do mês</a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="bg-white border border-slate-300 px-5 py-3 rounded-xl font-bold hover:bg-slate-50">Sair</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-900 px-5 py-4 rounded-2xl mb-6 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-900 px-5 py-4 rounded-2xl mb-6">
                <strong>Corrija os campos:</strong>
                <ul class="list-disc ml-6 mt-2">
                    @foreach($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="GET" action="{{ route('financeiro.index') }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-4 lg:p-5 mb-7">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-3 items-center">
                <input type="month" name="mes" value="{{ $mes }}" class="h-14 w-full rounded-xl border border-slate-300 px-4 text-base">

                <select name="tipo" class="h-14 w-full rounded-xl border border-slate-300 px-4 text-base">
                    <option value="todos" @selected($tipo === 'todos')>Todos os tipos</option>
                    <option value="receita" @selected($tipo === 'receita')>Receitas</option>
                    <option value="despesa" @selected($tipo === 'despesa')>Despesas</option>
                </select>

                <select name="origem" class="h-14 w-full rounded-xl border border-slate-300 px-4 text-base">
                    <option value="todas" @selected($origem === 'todas')>Todas as origens</option>
                    <option value="app" @selected($origem === 'app')>App</option>
                    <option value="governo" @selected($origem === 'governo')>Governo</option>
                    <option value="pessoal" @selected($origem === 'pessoal')>Pessoal</option>
                </select>

                <select name="categoria" class="h-14 w-full rounded-xl border border-slate-300 px-4 text-base">
                    <option value="todas" @selected($categoria === 'todas')>Todas as categorias</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat }}" @selected($categoria === $cat)>{{ $cat }}</option>
                    @endforeach
                </select>

                <button class="h-14 w-full bg-blue-600 text-white rounded-xl font-black hover:bg-blue-700">Filtrar</button>
                <a href="{{ route('financeiro.index') }}" class="h-14 w-full bg-slate-200 text-slate-950 rounded-xl font-black hover:bg-slate-300 flex items-center justify-center">Limpar</a>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
            <div class="bg-green-600 text-white rounded-3xl p-7 shadow-sm">
                <p>Receitas do mês</p>
                <p class="text-4xl font-black mt-2">{{ dinheiro($totalReceitas) }}</p>
            </div>
            <div class="bg-red-600 text-white rounded-3xl p-7 shadow-sm">
                <p>Despesas do mês</p>
                <p class="text-4xl font-black mt-2">{{ dinheiro($totalDespesas) }}</p>
            </div>
            <div class="bg-blue-600 text-white rounded-3xl p-7 shadow-sm">
                <p>Saldo do mês</p>
                <p class="text-4xl font-black mt-2">{{ dinheiro($saldo) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-7">
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm"><p class="text-slate-600">Receita App</p><p class="text-3xl font-black">{{ dinheiro($receitaApp) }}</p></div>
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm"><p class="text-slate-600">Receita Governo</p><p class="text-3xl font-black">{{ dinheiro($receitaGoverno) }}</p></div>
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm"><p class="text-slate-600">Custos do carro</p><p class="text-3xl font-black">{{ dinheiro($custosCarro) }}</p></div>
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm"><p class="text-slate-600">Lucro real App</p><p class="text-3xl font-black">{{ dinheiro($lucroApp) }}</p></div>
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm"><p class="text-slate-600">Média diária</p><p class="text-3xl font-black">{{ dinheiro($mediaDiaria) }}</p></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-7 mb-7">
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <h2 class="text-2xl font-black mb-5">Novo lançamento</h2>
                <form method="POST" action="{{ route('transacoes.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
                    @csrf
                    <input type="date" name="data" value="{{ now()->format('Y-m-d') }}" class="h-14 rounded-xl border border-slate-300 px-4" required>
                    <select name="tipo" class="h-14 rounded-xl border border-slate-300 px-4" required>
                        <option value="receita">Receita</option>
                        <option value="despesa">Despesa</option>
                    </select>
                    <select name="origem" class="h-14 rounded-xl border border-slate-300 px-4" required>
                        <option value="app">App</option>
                        <option value="governo">Governo</option>
                        <option value="pessoal">Pessoal</option>
                    </select>
                    <input type="text" name="categoria" placeholder="Categoria" class="h-14 rounded-xl border border-slate-300 px-4" required>
                    <input type="number" step="0.01" name="valor" placeholder="Valor" class="h-14 rounded-xl border border-slate-300 px-4" required>
                    <button class="h-14 rounded-xl bg-blue-600 text-white font-black hover:bg-blue-700">Salvar</button>
                    <input type="text" name="descricao" placeholder="Descrição" class="md:col-span-6 h-14 rounded-xl border border-slate-300 px-4">
                </form>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <h2 class="text-2xl font-black mb-5">Resumo inteligente</h2>
                <p class="text-slate-700">Custo do carro sobre receita App:</p>
                <p class="text-4xl font-black mb-5">{{ number_format($percentualCustoApp, 1, ',', '.') }}%</p>
                <p class="text-slate-700">Sobra sobre receita total:</p>
                <p class="text-4xl font-black mb-5">{{ number_format($percentualSobra, 1, ',', '.') }}%</p>
                <div class="bg-slate-100 rounded-2xl p-4 text-slate-700">
                    @if($receitaApp <= 0)
                        Cadastre suas receitas do app para o sistema calcular seu lucro real.
                    @elseif($custosCarro <= 0)
                        Cadastre combustível/manutenção como despesa do App para calcular o lucro real.
                    @else
                        Seu lucro real do app este mês é {{ dinheiro($lucroApp) }}.
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-7 mb-7">
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <h2 class="text-2xl font-black mb-5">Receitas x Despesas</h2>
                <canvas id="grafico"></canvas>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm">
                <h2 class="text-2xl font-black mb-5">Lançamentos</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-slate-100">
                                <th class="px-4 py-3">Data</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Origem</th>
                                <th class="px-4 py-3">Categoria</th>
                                <th class="px-4 py-3">Valor</th>
                                <th class="px-4 py-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transacoes as $t)
                                <tr class="border-t border-slate-100">
                                    <td class="px-4 py-3">{{ optional($t->data)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($t->tipo) }}</td>
                                    <td class="px-4 py-3">{{ $nomesOrigem[$t->origem] ?? $t->origem }}</td>
                                    <td class="px-4 py-3">{{ $t->categoria }}</td>
                                    <td class="px-4 py-3 font-bold">{{ dinheiro($t->valor) }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <form method="POST" action="{{ route('transacoes.destroy', $t) }}" onsubmit="return confirm('Excluir este lançamento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 font-bold hover:underline">Excluir</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">Nenhum lançamento encontrado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('grafico');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($graficoLabels),
            datasets: [
                { label: 'Receitas', data: @json($graficoReceitas) },
                { label: 'Despesas', data: @json($graficoDespesas) }
            ]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
</body>
</html>
