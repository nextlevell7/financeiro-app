<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-5xl mx-auto py-10">

    <div class="bg-white p-6 rounded-2xl shadow">

        <h1 class="text-3xl font-bold mb-2">📊 Relatório do mês</h1>
        <p class="text-gray-500 mb-6">
            Referente a {{ $mes }}
        </p>

        <a href="{{ route('financeiro.index') }}"
           class="inline-block mb-6 px-4 py-2 bg-blue-600 text-white rounded-xl">
            ← Voltar
        </a>

        <!-- Cards -->
        <div class="grid grid-cols-3 gap-4 mb-6">

            <div class="bg-green-500 text-white p-4 rounded-xl">
                <p>Receitas</p>
                <h2 class="text-2xl font-bold">
                    R$ {{ number_format($totalReceitas, 2, ',', '.') }}
                </h2>
            </div>

            <div class="bg-red-500 text-white p-4 rounded-xl">
                <p>Despesas</p>
                <h2 class="text-2xl font-bold">
                    R$ {{ number_format($totalDespesas, 2, ',', '.') }}
                </h2>
            </div>

            <div class="bg-blue-500 text-white p-4 rounded-xl">
                <p>Saldo</p>
                <h2 class="text-2xl font-bold">
                    R$ {{ number_format($saldo, 2, ',', '.') }}
                </h2>
            </div>

        </div>

        <!-- Tabela -->
        <div class="overflow-x-auto">
            <table class="w-full border rounded-xl overflow-hidden">

                <thead class="bg-gray-200">
                <tr>
                    <th class="p-2 text-left">Data</th>
                    <th class="p-2 text-left">Tipo</th>
                    <th class="p-2 text-left">Origem</th>
                    <th class="p-2 text-left">Categoria</th>
                    <th class="p-2 text-left">Descrição</th>
                    <th class="p-2 text-right">Valor</th>
                </tr>
                </thead>

                <tbody>
                @foreach($transacoes as $t)
                    <tr class="border-t">
                        <td class="p-2">{{ \Carbon\Carbon::parse($t->data)->format('d/m/Y') }}</td>
                        <td class="p-2">{{ ucfirst($t->tipo) }}</td>
                        <td class="p-2">{{ $t->origem }}</td>
                        <td class="p-2">{{ $t->categoria }}</td>
                        <td class="p-2">{{ $t->descricao }}</td>
                        <td class="p-2 text-right">
                            R$ {{ number_format($t->valor, 2, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

    </div>

</div>

</body>
</html>