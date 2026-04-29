<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle Financeiro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .money { letter-spacing: -0.04em; }
    </style>
</head>
<body class="bg-slate-100 text-slate-950">
    @php
        function dinheiro($valor) { return 'R$ ' . number_format((float) $valor, 2, ',', '.'); }
        $nomesTipo = ['receita' => 'Receita', 'despesa' => 'Despesa'];
        $nomesOrigem = ['app' => 'App', 'governo' => 'Governo', 'pessoal' => 'Pessoal', 'outros' => 'Outros'];
    @endphp

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <section class="grid grid-cols-1 lg:grid-cols-[330px_1fr] gap-6 items-start mb-7">
            <div>
                <p class="text-blue-700 font-black text-sm">Sistema pessoal</p>
                <h1 class="text-4xl lg:text-5xl font-black leading-none mt-2">💰 Controle<br>Financeiro</h1>
                <p class="text-slate-600 mt-4 text-lg leading-relaxed">Ganhos, gastos, saldo e lucro real como motorista de app.</p>
                <a href="{{ route('financeiro.relatorio', request()->query()) }}" class="inline-flex mt-5 bg-slate-950 text-white px-5 py-3 rounded-xl font-black hover:bg-slate-800">Relatório do mês</a>
            </div>

            <form method="GET" action="{{ route('financeiro.index') }}" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-4 lg:p-5 w-full">
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-[160px_160px_180px_190px_110px_110px] gap-3 items-center">
                    <input type="month" name="mes" value="{{ $mes }}" class="h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <select name="tipo" class="h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="todos" @selected($tipo === 'todos')>Todos os tipos</option>
                        <option value="receita" @selected($tipo === 'receita')>Receitas</option>
                        <option value="despesa" @selected($tipo === 'despesa')>Despesas</option>
                    </select>

                    <select name="origem" class="h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="todos" @selected($origem === 'todos')>Todas as origens</option>
                        <option value="app" @selected($origem === 'app')>App</option>
                        <option value="governo" @selected($origem === 'governo')>Governo</option>
                        <option value="pessoal" @selected($origem === 'pessoal')>Pessoal</option>
                        <option value="outros" @selected($origem === 'outros')>Outros</option>
                    </select>

                    <select name="categoria" class="h-14 w-full rounded-xl border border-slate-300 bg-white px-4 text-base focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="todos" @selected($categoria === 'todos')>Todas as categorias</option>
                        @foreach($categorias as $key => $nome)
                            <option value="{{ $key }}" @selected($categoria === $key)>{{ $nome }}</option>
                        @endforeach
                    </select>

                    <button class="h-14 w-full rounded-xl bg-blue-600 text-white font-black hover:bg-blue-700">Filtrar</button>
                    <a href="{{ route('financeiro.index') }}" class="h-14 w-full rounded-xl bg-slate-200 text-slate-950 font-black hover:bg-slate-300 flex items-center justify-center">Limpar</a>
                </div>
            </form>
        </section>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-200 text-emerald-900 rounded-2xl px-5 py-4 mb-6 font-semibold">{{ session('success') }}</div>
        @endif

        <section class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
            <div class="rounded-3xl bg-green-600 text-white p-7 shadow-sm"><p>Receitas do mês</p><p class="text-4xl font-black money mt-2">{{ dinheiro($totalReceitas) }}</p></div>
            <div class="rounded-3xl bg-red-600 text-white p-7 shadow-sm"><p>Despesas do mês</p><p class="text-4xl font-black money mt-2">{{ dinheiro($totalDespesas) }}</p></div>
            <div class="rounded-3xl bg-blue-600 text-white p-7 shadow-sm"><p>Saldo do mês</p><p class="text-4xl font-black money mt-2">{{ dinheiro($saldo) }}</p></div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-7">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5"><p class="text-slate-600">Receita App</p><p class="text-3xl font-black money">{{ dinheiro($receitaApp) }}</p></div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5"><p class="text-slate-600">Receita Governo</p><p class="text-3xl font-black money">{{ dinheiro($receitaGoverno) }}</p></div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5"><p class="text-slate-600">Custos do carro</p><p class="text-3xl font-black money">{{ dinheiro($custosCarro) }}</p></div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5"><p class="text-slate-600">Lucro real App</p><p class="text-3xl font-black money">{{ dinheiro($lucroApp) }}</p></div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5"><p class="text-slate-600">Média diária</p><p class="text-3xl font-black money">{{ dinheiro($mediaDiaria) }}</p></div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6 mb-7">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-2xl font-black mb-5">Novo lançamento</h2>
                <form method="POST" action="{{ route('transacoes.store') }}" class="grid grid-cols-1 md:grid-cols-[140px_130px_130px_1fr_130px_110px] gap-3">
                    @csrf
                    <input type="date" name="data" value="{{ now()->toDateString() }}" required class="h-14 rounded-xl border border-slate-300 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <select name="tipo" required class="h-14 rounded-xl border border-slate-300 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="receita">Receita</option>
                        <option value="despesa">Despesa</option>
                    </select>
                    <select name="origem" required class="h-14 rounded-xl border border-slate-300 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="app">App</option>
                        <option value="governo">Governo</option>
                        <option value="pessoal">Pessoal</option>
                        <option value="outros">Outros</option>
                    </select>
                    <select name="categoria" required class="h-14 rounded-xl border border-slate-300 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($categorias as $key => $nome)
                            <option value="{{ $key }}">{{ $nome }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" min="0.01" name="valor" placeholder="Valor" required class="h-14 rounded-xl border border-slate-300 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button class="h-14 rounded-xl bg-blue-600 text-white font-black hover:bg-blue-700">Salvar</button>
                    <input type="text" name="descricao" placeholder="Descrição" class="md:col-span-6 h-14 rounded-xl border border-slate-300 px-4 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
                </form>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-2xl font-black mb-5">Resumo inteligente</h2>
                <p class="text-slate-700">Custo do carro sobre receita App:</p>
                <p class="text-4xl font-black mt-1 mb-5">{{ number_format($percentualCustoApp, 1, ',', '.') }}%</p>
                <p class="text-slate-700">Sobra sobre receita total:</p>
                <p class="text-4xl font-black mt-1 mb-5">{{ number_format($percentualSobra, 1, ',', '.') }}%</p>
                <div class="bg-slate-100 rounded-2xl p-4 text-slate-700">
                    @if($receitaApp <= 0)
                        Cadastre suas receitas para o sistema começar a calcular sua vida financeira real.
                    @elseif($percentualCustoApp >= 40)
                        Atenção: o custo do carro está alto em relação ao que você faturou no app.
                    @else
                        Bom controle: seu custo do carro está dentro de uma faixa mais saudável.
                    @endif
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 lg:grid-cols-[1fr_380px] gap-6 mb-7">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-2xl font-black mb-5">Receitas x Despesas</h2>
                <canvas id="graficoMensal" height="110"></canvas>
            </div>
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6">
                <h2 class="text-2xl font-black mb-5">Insight do mês</h2>
                <p class="text-slate-700 leading-relaxed">
                    Seu saldo no mês está em <strong>{{ dinheiro($saldo) }}</strong>. Seu lucro real no app está em <strong>{{ dinheiro($lucroApp) }}</strong> após os custos do carro.
                </p>
            </div>
        </section>

        <section class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-6 flex items-center justify-between gap-4">
                <h2 class="text-2xl font-black">Lançamentos</h2>
                <p class="text-slate-600">{{ $transacoes->count() }} registro(s)</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="bg-slate-200">
                        <tr>
                            <th class="px-6 py-4">Data</th>
                            <th class="px-6 py-4">Tipo</th>
                            <th class="px-6 py-4">Origem</th>
                            <th class="px-6 py-4">Categoria</th>
                            <th class="px-6 py-4">Descrição</th>
                            <th class="px-6 py-4">Valor</th>
                            <th class="px-6 py-4 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transacoes as $t)
                            <tr class="border-t border-slate-100">
                                <td class="px-6 py-4">{{ \Carbon\Carbon::parse($t->data)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-sm font-bold {{ $t->tipo === 'receita' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $nomesTipo[$t->tipo] ?? $t->tipo }}</span></td>
                                <td class="px-6 py-4">{{ $nomesOrigem[$t->origem] ?? $t->origem }}</td>
                                <td class="px-6 py-4">{{ $categorias[$t->categoria] ?? $t->categoria }}</td>
                                <td class="px-6 py-4">{{ $t->descricao ?: '-' }}</td>
                                <td class="px-6 py-4 font-black money">{{ dinheiro($t->valor) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('transacoes.destroy', $t) }}" onsubmit="return confirm('Excluir este lançamento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 font-bold hover:underline">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-slate-500">Nenhum lançamento encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script>
        const ctx = document.getElementById('graficoMensal');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels),
                datasets: [
                    { label: 'Receitas', data: @json($receitasGrafico), borderWidth: 1 },
                    { label: 'Despesas', data: @json($despesasGrafico), borderWidth: 1 }
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
