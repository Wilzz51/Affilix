@extends('layouts.client')

@section('title', __('Affilix::affiliation.referrals'))

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
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Parrainages') }}</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">{{ $affiliate->referral_code }}</p>
        </div>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.commissions') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-cash-stack mr-1"></i>{{ __('Commissions') }}
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
            <div class="h-11 w-11 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-people-fill text-purple-600 dark:text-purple-400"></i>
            </div>
            <div>
                <p class="text-xl font-bold tracking-tight text-purple-600 dark:text-purple-400">{{ number_format($affiliate->total_referrals) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Total') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-trophy-fill text-green-600 dark:text-green-400"></i>
            </div>
            <div>
                <p class="text-xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ number_format($affiliate->successful_referrals) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Convertis') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-graph-up-arrow text-orange-600 dark:text-orange-400"></i>
            </div>
            <div>
                <p class="text-xl font-bold tracking-tight text-orange-600 dark:text-orange-400">{{ number_format($affiliate->getConversionRate(), 1) }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Conversion') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Lien --}}
<div class="rounded-2xl bg-gradient-to-br from-primary/8 to-primary/3 dark:from-primary/15 dark:to-primary/5 border border-primary/20 dark:border-primary/25 p-5">
    <div class="flex items-center gap-2 mb-3">
        <div class="h-8 w-8 rounded-xl bg-primary/15 flex items-center justify-center shrink-0">
            <i class="bi bi-link-45deg text-primary text-sm"></i>
        </div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Affilix::affiliation.referral_link') }}</p>
    </div>
    <div class="flex gap-2">
        <input type="text" id="referral-link" value="{{ $affiliate->getReferralUrl() }}" readonly
            class="flex-1 rounded-xl border border-primary/25 bg-white/70 dark:bg-gray-800/70 text-gray-700 dark:text-gray-300 px-4 py-2.5 text-sm font-mono focus:outline-none truncate">
        <button onclick="copyLink(this)" class="btn btn-primary rounded-xl shrink-0 flex items-center gap-2 px-4">
            <i class="bi bi-clipboard text-sm"></i>
            <span class="hidden sm:inline">{{ __('Copier') }}</span>
        </button>
    </div>
</div>

{{-- Tableau --}}
<div class="card shadow-sm">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <div class="h-7 w-7 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-people text-purple-600 dark:text-purple-400 text-xs"></i>
            </div>
            <h4 class="text-sm">{{ __('Liste des parrainages') }}</h4>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Client') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Inscription') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('1er achat') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Commissions') }}</th>
                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($referrals as $referral)
                @php
                    $firstname = $referral->customer->firstname ?? $referral->customer->name ?? '';
                    $lastname  = $referral->customer->lastname ?? '';
                    $masked    = trim($firstname . ($lastname ? ' ' . strtoupper(substr($lastname, 0, 1)) . '.' : '')) ?: '—';
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $masked }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $referral->registered_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $referral->first_purchase_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap text-gray-900 dark:text-white">
                        {{ number_format($referral->commissions->sum('amount'), 2) }} {{ setting('currency_symbol', '€') }}
                    </td>
                    <td class="px-4 py-3">
                        @if($referral->status === 'converted')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-trophy text-[9px]"></i>{{ __('Converti') }}
                            </span>
                        @elseif($referral->status === 'registered')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                                <i class="bi bi-person-check text-[9px]"></i>{{ __('Inscrit') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 whitespace-nowrap">
                                <i class="bi bi-cursor text-[9px]"></i>{{ __('Clic') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="bi bi-people text-2xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucun parrainage pour le moment') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($referrals->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
        {{ $referrals->links() }}
    </div>
    @endif
</div>

</div>

<script>
function copyLink(btn) {
    const val = document.getElementById('referral-link').value;
    navigator.clipboard.writeText(val).catch(() => { const el = document.getElementById('referral-link'); el.select(); document.execCommand('copy'); });
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-clipboard-check text-sm"></i><span class="hidden sm:inline">{{ __("Copié !") }}</span>';
    setTimeout(() => btn.innerHTML = orig, 2000);
}
</script>
@endsection
