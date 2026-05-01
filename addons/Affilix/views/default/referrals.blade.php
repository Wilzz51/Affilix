@extends('layouts.client')

@section('title', __('Affilix::affiliation.referrals'))

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
            <h4>{{ __('Affilix::affiliation.referrals') }}</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Code :') }} <strong class="font-mono">{{ $affiliate->referral_code }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('affiliation.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-speedometer2 mr-1"></i>{{ __('Dashboard') }}
            </a>
            <a href="{{ route('affiliation.commissions') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-cash-stack mr-1"></i>{{ __('Commissions') }}
            </a>
            <a href="{{ route('affiliation.settings') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-gear mr-1"></i>{{ __('Paramètres') }}
            </a>
        </div>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($affiliate->total_referrals) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Affilix::affiliation.stats.total_referrals') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($affiliate->successful_referrals) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Affilix::affiliation.stats.successful_referrals') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($affiliate->getConversionRate(), 1) }}%</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('Affilix::affiliation.stats.conversion_rate') }}</p>
        </div>
    </div>
</div>

{{-- Lien de parrainage --}}
<div class="card mb-4">
    <div class="card-body">
        <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">
            {{ __('Affilix::affiliation.referral_link') }}
        </p>
        <div class="flex gap-2">
            <input type="text" id="referral-link" value="{{ $affiliate->getReferralUrl() }}" readonly
                class="flex-1 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-2 text-sm font-mono focus:outline-none">
            <button onclick="copyLink(this)" class="btn btn-primary rounded-l-none">
                <i class="bi bi-clipboard mr-1"></i>{{ __('Copier') }}
            </button>
        </div>
    </div>
</div>

{{-- Tableau des parrainages --}}
<div class="card">
    <div class="card-heading">
        <h4>{{ __('Liste des parrainages') }}</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.customer') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.registered_at') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.first_purchase_at') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Commissions') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($referrals as $referral)
                @php
                    $firstname = $referral->customer->firstname ?? $referral->customer->name ?? '';
                    $lastname  = $referral->customer->lastname ?? '';
                    $masked    = trim($firstname . ($lastname ? ' ' . strtoupper(substr($lastname, 0, 1)) . '.' : '')) ?: '—';
                @endphp
                <tr>
                    <td class="px-4 py-3 text-sm font-medium">{{ $masked }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $referral->registered_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $referral->first_purchase_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap">
                        {{ number_format($referral->commissions->sum('amount'), 2) }} {{ setting('currency_symbol', '€') }}
                    </td>
                    <td class="px-4 py-3">
                        @if($referral->status === 'converted')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-trophy text-[10px]"></i>{{ __('Affilix::affiliation.converted') }}
                            </span>
                        @elseif($referral->status === 'registered')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                                <i class="bi bi-person-check text-[10px]"></i>{{ __('Affilix::affiliation.registered') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                <i class="bi bi-cursor text-[10px]"></i>{{ __('Affilix::affiliation.clicked') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-sm text-gray-400 dark:text-gray-500 py-8">
                        <i class="bi bi-people text-2xl block mb-2"></i>
                        {{ __('Aucun parrainage pour le moment') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($referrals->hasPages())
    <div class="px-4 py-3">
        {{ $referrals->links() }}
    </div>
    @endif
</div>

</div>

<script>
function copyLink(btn) {
    const val = document.getElementById('referral-link').value;
    navigator.clipboard.writeText(val).catch(() => {
        const el = document.getElementById('referral-link');
        el.select();
        document.execCommand('copy');
    });
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-clipboard-check mr-1"></i>{{ __("Copié !") }}';
    setTimeout(() => btn.innerHTML = orig, 2000);
}
</script>
@endsection
