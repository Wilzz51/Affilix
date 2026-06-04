@extends('layouts.client')

@section('title', __('Affilix::affiliation.commissions'))

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-5">

{{-- Flash --}}
@if(session('success'))
    <div class="rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 text-sm text-green-700 dark:text-green-300 flex items-center gap-2.5">
        <i class="bi bi-check-circle-fill shrink-0"></i>{{ session('success') }}
    </div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('affiliation.dashboard') }}"
            class="h-9 w-9 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors shrink-0">
            <i class="bi bi-arrow-left text-gray-600 dark:text-gray-400 text-sm"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Commissions') }}</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">{{ $affiliate->referral_code }}</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.referrals') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-people mr-1"></i>{{ __('Parrainages') }}
        </a>
        <a href="{{ route('affiliation.settings') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-gear mr-1"></i>{{ __('Paramètres') }}
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                <i class="bi bi-graph-up text-gray-600 dark:text-gray-300"></i>
            </div>
            <div>
                <p class="text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ number_format($affiliate->total_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Total') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div>
                <p class="text-xl font-bold tracking-tight text-yellow-600 dark:text-yellow-400">{{ number_format($affiliate->pending_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('En attente') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle-fill text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ number_format($affiliate->paid_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Payé') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Tableau --}}
<div class="card shadow-sm">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <div class="h-7 w-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <i class="bi bi-receipt text-gray-500 dark:text-gray-400 text-xs"></i>
            </div>
            <h4 class="text-sm">{{ __('Historique') }}</h4>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Montant') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Taux') }}</th>
                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($commissions as $commission)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $commission->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $commission->description }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap text-gray-900 dark:text-white">+{{ number_format($commission->amount, 2) }} {{ setting('currency_symbol', '€') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                        @if($commission->commission_rate > 0)
                            {{ number_format($commission->commission_rate, 0) }}%
                        @else
                            <span class="text-gray-300 dark:text-gray-600">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($commission->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-check-circle-fill text-[9px]"></i>{{ __('Payé') }}
                            </span>
                        @elseif($commission->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                                <i class="bi bi-check-circle text-[9px]"></i>{{ __('Approuvé') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-clock text-[9px]"></i>{{ __('En attente') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="bi bi-cash-stack text-2xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucune commission pour le moment') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
        {{ $commissions->links() }}
    </div>
    @endif
</div>

</div>
@endsection
