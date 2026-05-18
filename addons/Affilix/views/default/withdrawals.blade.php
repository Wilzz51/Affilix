@extends('layouts.client')

@section('title', __('Demandes de paiement'))

@section('content')
<div class="max-w-4xl mx-auto py-6">

{{-- En-tête --}}
<div class="card mb-4">
    <div class="card-heading">
        <div>
            <h4>{{ __('Mes demandes de paiement') }}</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Historique de vos retraits') }}</p>
        </div>
        <a href="{{ route('affiliation.dashboard') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left mr-1"></i>{{ __('Tableau de bord') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-4 text-sm text-green-700 dark:text-green-300">
        <i class="bi bi-check-circle mr-1"></i>{{ session('success') }}
    </div>
@endif

<div class="card">
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-4 py-3">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Montant') }}</th>
                    <th class="px-4 py-3">{{ __('Méthode') }}</th>
                    <th class="px-4 py-3">{{ __('Référence') }}</th>
                    <th class="px-4 py-3">{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $withdrawal)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $withdrawal->created_at->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap">
                        {{ number_format($withdrawal->amount, 2) }} {{ setting('currency_symbol', '€') }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        @if($withdrawal->payment_method === 'balance')
                            <span class="flex items-center gap-1"><i class="bi bi-wallet2 text-blue-500"></i>{{ __('Solde') }}</span>
                        @elseif($withdrawal->payment_method === 'paypal')
                            <span class="flex items-center gap-1"><i class="bi bi-paypal text-indigo-500"></i>PayPal</span>
                        @else
                            <span class="flex items-center gap-1"><i class="bi bi-bank text-gray-400"></i>{{ __('Virement') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($withdrawal->payment_reference)
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded font-mono">{{ $withdrawal->payment_reference }}</code>
                        @elseif($withdrawal->admin_note)
                            <span class="text-xs text-red-500 italic">{{ $withdrawal->admin_note }}</span>
                        @else
                            <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($withdrawal->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-hourglass-split text-[10px]"></i>{{ __('En attente') }}
                            </span>
                        @elseif($withdrawal->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-check-circle text-[10px]"></i>{{ __('Payé') }}
                            </span>
                            @if($withdrawal->paid_at)
                                <span class="block text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $withdrawal->paid_at->format('d/m/Y') }}</span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">
                                <i class="bi bi-x-circle text-[10px]"></i>{{ __('Rejeté') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-sm text-gray-400 dark:text-gray-500 py-8">
                        <i class="bi bi-wallet2 text-3xl block mb-2 opacity-40"></i>
                        {{ __('Aucune demande de paiement') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $withdrawals->links() }}
        </div>
    @endif
</div>

</div>
@endsection
