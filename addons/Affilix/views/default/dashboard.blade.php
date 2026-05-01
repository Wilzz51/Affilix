@extends('layouts.client')

@section('title', __('Affilix::affiliation.dashboard'))

@section('content')
<div class="max-w-6xl mx-auto py-6">

@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-4 text-sm text-green-700 dark:text-green-300">
        <i class="bi bi-check-circle mr-1"></i>{{ session('success') }}
    </div>
@endif
@if(session('info'))
    <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4 mb-4 text-sm text-blue-700 dark:text-blue-300">
        <i class="bi bi-info-circle mr-1"></i>{{ session('info') }}
    </div>
@endif

{{-- En-tête + navigation --}}
<div class="card mb-4">
    <div class="card-heading">
        <div>
            <h4>{{ __('Affilix::affiliation.my_affiliate_account') }}</h4>
            <p class="flex items-center gap-3 flex-wrap mt-1 text-sm text-gray-500 dark:text-gray-400">
                <span>{{ __('Code :') }} <strong class="font-mono text-gray-700 dark:text-gray-300">{{ $affiliate->referral_code }}</strong></span>
                <span class="hidden sm:inline text-gray-300 dark:text-gray-600">·</span>
                <span>{{ __('Commission :') }} <strong class="text-gray-700 dark:text-gray-300">{{ number_format($affiliate->commission_rate, 0) }}%</strong></span>
                @if($affiliate->status === 'active')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                        <i class="bi bi-check-circle mr-1"></i>{{ __('Affilix::affiliation.active') }}
                    </span>
                @elseif($affiliate->status === 'inactive')
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                        <i class="bi bi-clock mr-1"></i>{{ __('En attente d\'approbation') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                        <i class="bi bi-x-circle mr-1"></i>{{ __('Affilix::affiliation.suspended') }}
                    </span>
                @endif
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('affiliation.commissions') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-cash-stack mr-1"></i>{{ __('Affilix::affiliation.commissions') }}
            </a>
            <a href="{{ route('affiliation.referrals') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-people mr-1"></i>{{ __('Affilix::affiliation.referrals') }}
            </a>
            <a href="{{ route('affiliation.settings') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-gear mr-1"></i>{{ __('Affilix::affiliation.settings') }}
            </a>
        </div>
    </div>
</div>

{{-- Lien de parrainage --}}
<div class="card mb-4">
    <div class="card-body">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-2">
            <i class="bi bi-link-45deg mr-1"></i>{{ __('Affilix::affiliation.referral_link') }}
        </p>
        <div class="flex gap-2">
            <input type="text" id="referral-link" value="{{ $affiliate->getReferralUrl() }}" readonly
                class="flex-1 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-2 text-sm font-mono focus:outline-none">
            <button onclick="copyLink(this)" class="btn btn-primary rounded-l-none">
                <i class="bi bi-clipboard mr-1"></i>{{ __('Affilix::affiliation.copy_link') }}
            </button>
        </div>
    </div>
</div>

{{-- Statistiques principales --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
    <div class="card">
        <div class="card-body text-center">
            <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                <i class="bi bi-cursor-fill text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_clicks']) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.stats.total_clicks') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                <i class="bi bi-people-fill text-purple-600 dark:text-purple-400 text-lg"></i>
            </div>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_referrals']) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.stats.total_referrals') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                <i class="bi bi-graph-up-arrow text-orange-600 dark:text-orange-400 text-lg"></i>
            </div>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($stats['conversion_rate'], 1) }}%</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.stats.conversion_rate') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                <i class="bi bi-cash-coin text-green-600 dark:text-green-400 text-lg"></i>
            </div>
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['total_earnings'], 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.stats.total_earnings') }}</p>
        </div>
    </div>
</div>

{{-- Gains détaillés --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
    <div class="card">
        <div class="card-body flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle-fill text-green-600 dark:text-green-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide truncate">{{ __('Affilix::affiliation.stats.paid_earnings') }}</p>
                <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['paid_earnings'], 2) }} {{ setting('currency_symbol', '€') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide truncate">{{ __('Affilix::affiliation.stats.pending_earnings') }}</p>
                <p class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($stats['pending_earnings'], 2) }} {{ setting('currency_symbol', '€') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-trophy-fill text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide truncate">{{ __('Affilix::affiliation.stats.successful_referrals') }}</p>
                <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['successful_referrals']) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Dernières commissions --}}
<div class="card">
    <div class="card-heading">
        <h4>{{ __('Dernières commissions') }}</h4>
        <a href="{{ route('affiliation.commissions') }}" class="btn btn-secondary btn-sm">
            {{ __('Voir tout') }}<i class="bi bi-arrow-right ml-1"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.date') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.description') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Affilix::affiliation.amount') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentCommissions as $commission)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $commission->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm">{{ $commission->description }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap">{{ number_format($commission->amount, 2) }} {{ setting('currency_symbol', '€') }}</td>
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
                    <td colspan="4" class="text-center text-sm text-gray-400 dark:text-gray-500 py-8">
                        <i class="bi bi-cash-stack text-3xl block mb-2 opacity-40"></i>
                        {{ __('Aucune commission pour le moment') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
