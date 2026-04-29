@extends('layouts.client')

@section('title', __('Affilix::affiliation.commissions'))

@section('content')
<div class="max-w-5xl mx-auto py-6">

{{-- Flash --}}
@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-4 text-sm text-green-700 dark:text-green-300">
        {{ session('success') }}
    </div>
@endif

{{-- Navigation --}}
<div class="card mb-4">
    <div class="card-heading">
        <div>
            <h4>{{ __('Affilix::affiliation.commissions') }}</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Code :') }} <strong class="font-mono">{{ $affiliate->referral_code }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('affiliation.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-speedometer2 mr-1"></i>{{ __('Dashboard') }}
            </a>
            <a href="{{ route('affiliation.referrals') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-people mr-1"></i>{{ __('Parrainages') }}
            </a>
            <a href="{{ route('affiliation.settings') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-gear mr-1"></i>{{ __('Paramètres') }}
            </a>
        </div>
    </div>
</div>

{{-- Résumé gains --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($affiliate->total_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Affilix::affiliation.stats.total_earnings') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format($affiliate->pending_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Affilix::affiliation.stats.pending_earnings') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($affiliate->paid_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Affilix::affiliation.stats.paid_earnings') }}</p>
        </div>
    </div>
</div>

{{-- Tableau des commissions --}}
<div class="card">
    <div class="card-heading">
        <h4>{{ __('Historique des commissions') }}</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.date') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.description') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Affilix::affiliation.amount') }}</th>
                    <th class="px-4 py-3 text-center">{{ __('Affilix::affiliation.stats.commission_rate') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $commission)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $commission->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $commission->description }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap">{{ number_format($commission->amount, 2) }} {{ setting('currency_symbol', '€') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">{{ number_format($commission->commission_rate, 0) }}%</td>
                    <td class="px-4 py-3">
                        @if($commission->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-check-circle text-[10px]"></i>{{ __('Affilix::affiliation.paid') }}
                            </span>
                        @elseif($commission->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                                <i class="bi bi-check text-[10px]"></i>{{ __('Affilix::affiliation.approved') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-clock text-[10px]"></i>{{ __('Affilix::affiliation.pending') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-sm text-gray-400 dark:text-gray-500 py-8">
                        <i class="bi bi-cash-stack text-2xl block mb-2"></i>
                        {{ __('Aucune commission pour le moment') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
    <div class="px-4 py-3">
        {{ $commissions->links() }}
    </div>
    @endif
</div>

</div>
@endsection
