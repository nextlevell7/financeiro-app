<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro Mensal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .page { box-shadow: none !important; border: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900">
    <main class="max-w-5xl mx-auto p-6">
        <div class="no-print mb-4 flex gap-2">
            <button onclick="window.print()" class="bg-blue-600 text-white font-bold px-5 py-3 rounded-xl">Imprimir / Salvar PDF</button>
            <a href="{{ route('financeiro.index', ['mes' => $mes]) }}" class="bg-slate-200 font-bold px-5 py-3 rounded-xl">Voltar</a>
        </div>

        <section class="page bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
            <header class="border-b pb-6 mb-6">
                <p class="text-sm text-blue-700 font-bold">Sistema pessoal</p>
                <h1 class="text-3xl font-black">Relatório Financeiro Mensal</h1>
                <p class="text-slate-600">Período: {{ $inicio->format('d/m/Y') }} a {{ $fim->format('d/m/Y') }}</p>
            </header>

            <section class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
                    <p class="text-green-700">Receitas</p>
                    <h2 class="text-2xl font-black">R$ {{ number_format($totalReceitas, 2, ',', '.') }}</h2>
                </div>
                <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                    <p class="text-red-700">Despesas</p>
                    <h2 class="text-2xl font-black">R$ {{ number_format($totalDespesas, 2, ',', '.') }}</h2>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                    <p class="text-blue-700">Saldo</p>
                    <h2 class="text-2xl font-black">R$ {{ number_format($saldo, 2, ',', '.') }}</h2>
                </div>
            </section>

            <section class="grid grid-cols-4 gap-4 mb-8">
                <div class="border rounded-2xl p-4">
                    <p class="text-slate-600">Receita App</p>
                    <strong>R$ {{ number_format($receitaApp, 2, ',', '.') }}</strong>
                </div>
                <div class="border rounded-2xl p-4">
                    <p class="text-slate-600">Receita Governo</p>
                    <strong>R$ {{ number_format($receitaGoverno, 2, ',', '.') }}</strong>
                </div>
                <div class="border rounded-2xl p-4">
                    <p class="text-slate-600">Custos carro</p>
                    <strong>R$ {{ number_format($custosCarro, 2, ',', '.') }}</strong>
                </div>
                <div class="border rounded-2xl p-4">
                    <p class="text-slate-600">Lucro App</p>
                    <strong>R$ {{ number_format($lucroApp, 2, ',', '.') }}</strong>
                </div>
            </section>

            <section class="mb-8">
                <h2 class="text-xl font-black mb-2">Resumo inteligente</h2>
                <p class="bg-slate-100 rounded-2xl p-4">{{ $insight }}</p>
            </section>

            <section>
                <h2 class="text-xl font-black mb-3">Lançamentos do mês</h2>
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-200">
                            <th class="p-2 border">Data</th>
                            <th class="p-2 border">Tipo</th>
                            <th class="p-2 border">Origem</th>
                            <th class="p-2 border">Categoria</th>
                            <th class="p-2 border">Descrição</th>
                            <th class="p-2 border">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transacoes as $t)
                            <tr>
                                <td class="p-2 border">{{ optional($t->data)->format('d/m/Y') }}</td>
                                <td class="p-2 border">{{ ucfirst($t->tipo) }}</td>
                                <td class="p-2 border">{{ ucfirst($t->origem) }}</td>
                                <td class="p-2 border">{{ ucfirst($t->categoria) }}</td>
                                <td class="p-2 border">{{ $t->descricao ?: '-' }}</td>
                                <td class="p-2 border font-bold">R$ {{ number_format($t->valor, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-4 border text-center">Nenhum lançamento cadastrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        </section>
    </main>
</body>
</html>
