<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório do mês</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-950 p-6">
@php
    function dinheiroRelatorio($valor) {
        return 'R$ ' . number_format((float) $valor, 2, ',', '.');
    }
@endphp
<div class="max-w-5xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-blue-700 font-black">Sistema pessoal</p>
            <h1 class="text-3xl font-black">Relatório do mês</h1>
            <p class="text-slate-600">Mês: {{ $mes }}</p>
        </div>
        <a href="{{ route('financeiro.index', ['mes' => $mes]) }}" class="bg-slate-950 text-white px-5 py-3 rounded-xl font-bold">Voltar</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-100 rounded-2xl p-5"><p>Receitas</p><strong class="text-2xl">{{ dinheiroRelatorio($totalReceitas) }}</strong></div>
        <div class="bg-red-100 rounded-2xl p-5"><p>Despesas</p><strong class="text-2xl">{{ dinheiroRelatorio($totalDespesas) }}</strong></div>
        <div class="bg-blue-100 rounded-2xl p-5"><p>Saldo</p><strong class="text-2xl">{{ dinheiroRelatorio($saldo) }}</strong></div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-slate-100">
                    <th class="px-4 py-3">Data</th>
                    <th class="px-4 py-3">Tipo</th>
                    <th class="px-4 py-3">Origem</th>
                    <th class="px-4 py-3">Categoria</th>
                    <th class="px-4 py-3">Descrição</th>
                    <th class="px-4 py-3">Valor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transacoes as $t)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ optional($t->data)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ ucfirst($t->tipo) }}</td>
                        <td class="px-4 py-3">{{ ucfirst($t->origem) }}</td>
                        <td class="px-4 py-3">{{ $t->categoria }}</td>
                        <td class="px-4 py-3">{{ $t->descricao }}</td>
                        <td class="px-4 py-3 font-bold">{{ dinheiroRelatorio($t->valor) }}</td>
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
</body>
</html>
