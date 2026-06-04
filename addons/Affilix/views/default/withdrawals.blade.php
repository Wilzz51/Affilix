@extends('layouts.client')

@section('title', __('Historique des retraits'))

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-5">

{{-- Flash --}}
@if(session('success'))
    <div class="rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 text-sm text-green-700 dark:text-green-300 flex items-center gap-2.5">
        <i class="bi bi-check-circle-fill shrink-0"></i>{{ session('success') }}
    </div>
@endif

{{-- Header --}}
<div class="flex items-center gap-3">
    <a href="{{ route('affiliation.dashboard') }}"
        class="h-9 w-9 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors shrink-0">
        <i class="bi bi-arrow-left text-gray-600 dark:text-gray-400 text-sm"></i>
    </a>
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Historique des retraits') }}</h1>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Toutes vos demandes de paiement') }}</p>
    </div>
</div>

{{-- Tableau --}}
<div class="card shadow-sm">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <div class="h-7 w-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <i class="bi bi-wallet2 text-gray-500 dark:text-gray-400 text-xs"></i>
            </div>
            <h4 class="text-sm">{{ __('Demandes de paiement') }}</h4>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Montant') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Méthode') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Référence') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($withdrawals as $withdrawal)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $withdrawal->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap text-gray-900 dark:text-white">{{ number_format($withdrawal->amount, 2) }} {{ setting('currency_symbol', '€') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        @if($withdrawal->payment_method === 'balance')
                            <span class="inline-flex items-center gap-1.5"><i class="bi bi-wallet2 text-blue-500"></i>{{ __('Solde') }}</span>
                        @elseif($withdrawal->payment_method === 'paypal')
                            <span class="inline-flex items-center gap-1.5"><i class="bi bi-paypal text-indigo-500"></i>PayPal</span>
                        @else
                            <span class="inline-flex items-center gap-1.5"><i class="bi bi-bank text-gray-400"></i>{{ __('Virement') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($withdrawal->payment_reference)
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-lg font-mono">{{ $withdrawal->payment_reference }}</code>
                        @elseif($withdrawal->admin_note)
                            <span class="text-xs text-red-500 italic">{{ $withdrawal->admin_note }}</span>
                        @else
                            <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($withdrawal->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-hourglass-split text-[9px]"></i>{{ __('En attente') }}
                            </span>
                        @elseif($withdrawal->status === 'paid')
                            <div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                    <i class="bi bi-check-circle-fill text-[9px]"></i>{{ __('Payé') }}
                                </span>
                                @if($withdrawal->paid_at)
                                    <span class="block text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $withdrawal->paid_at->format('d/m/Y') }}</span>
                                @endif
                            </div>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">
                                <i class="bi bi-x-circle text-[9px]"></i>{{ __('Rejeté') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="bi bi-wallet2 text-2xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucune demande de paiement') }}</p>
                        </div>
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
