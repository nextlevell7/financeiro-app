@extends('layouts.app')

@section('content')
@php
    if (! function_exists('dinheiro_relatorio')) {
        function dinheiro_relatorio($valor) {
            return 'R$ ' . number_format((float) $valor, 2, ',', '.');
        }
    }
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <p class="text-blue-700 font-black text-sm">Sistema pessoal</p>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-950">Relatório do mês</h1>
            <p class="text-slate-600 mt-2">Resumo financeiro referente a {{ $mes ?? now()->format('Y-m') }}.</p>
        </div>

        <a href="{{ route('financeiro.index', ['mes' => $mes ?? now()->format('Y-m')]) }}"
           class="inline-flex items-center justify-center rounded-xl bg-slate-950 px-5 py-3 text-white font-bold hover:bg-slate-800">
            Voltar ao dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-600 text-white rounded-2xl p-6 shadow-sm">
            <p class="text-sm">Receitas do mês</p>
            <p class="text-3xl font-black mt-2">{{ dinheiro_relatorio($totalReceitas ?? 0) }}</p>
        </div>

        <div class="bg-red-600 text-white rounded-2xl p-6 shadow-sm">
            <p class="text-sm">Despesas do mês</p>
            <p class="text-3xl font-black mt-2">{{ dinheiro_relatorio($totalDespesas ?? 0) }}</p>
        </div>

        <div class="bg-blue-600 text-white rounded-2xl p-6 shadow-sm">
            <p class="text-sm">Saldo do mês</p>
            <p class="text-3xl font-black mt-2">{{ dinheiro_relatorio($saldo ?? 0) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-xl font-black">Lançamentos</h2>
            <span class="text-sm text-slate-500">{{ isset($transacoes) ? $transacoes->count() : 0 }} registro(s)</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase text-slate-600">Data</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase text-slate-600">Tipo</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase text-slate-600">Origem</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase text-slate-600">Categoria</th>
                        <th class="px-4 py-3 text-left text-xs font-black uppercase text-slate-600">Descrição</th>
                        <th class="px-4 py-3 text-right text-xs font-black uppercase text-slate-600">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse(($transacoes ?? collect()) as $t)
                        <tr>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ \Carbon\Carbon::parse($t->data)->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-sm font-bold {{ $t->tipo === 'receita' ? 'text-green-700' : 'text-red-700' }}">
                                {{ ucfirst($t->tipo) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ ucfirst($t->origem ?? 'pessoal') }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $t->categoria }}</td>
                            <td class="px-4 py-3 text-sm text-slate-700">{{ $t->descricao ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right font-black text-slate-950">{{ dinheiro_relatorio($t->valor) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                Nenhum lançamento encontrado para este mês.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
