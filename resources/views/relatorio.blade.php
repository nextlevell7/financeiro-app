@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6">Relatório do mês</h1>

    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-green-500 text-white p-4 rounded">
            Receitas: R$ {{ number_format($totalReceitas, 2, ',', '.') }}
        </div>
        <div class="bg-red-500 text-white p-4 rounded">
            Despesas: R$ {{ number_format($totalDespesas, 2, ',', '.') }}
        </div>
        <div class="bg-blue-500 text-white p-4 rounded">
            Saldo: R$ {{ number_format($saldo, 2, ',', '.') }}
        </div>
    </div>

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th>Data</th>
                <th>Tipo</th>
                <th>Categoria</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transacoes as $t)
                <tr>
                    <td>{{ $t->data }}</td>
                    <td>{{ $t->tipo }}</td>
                    <td>{{ $t->categoria }}</td>
                    <td>R$ {{ number_format($t->valor, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection