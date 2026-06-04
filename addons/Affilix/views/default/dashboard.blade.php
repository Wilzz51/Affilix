@extends('layouts.client')

@section('title', __('Affilix::affiliation.dashboard'))

@section('content')
<div class="max-w-6xl mx-auto py-6 space-y-5">

{{-- Flash --}}
@if(session('success'))
    <div class="rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 text-sm text-green-700 dark:text-green-300 flex items-center gap-2.5">
        <i class="bi bi-check-circle-fill shrink-0"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 text-sm text-red-700 dark:text-red-300 flex items-center gap-2.5">
        <i class="bi bi-exclamation-circle-fill shrink-0"></i>{{ session('error') }}
    </div>
@endif
@if(session('info'))
    <div class="rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4 text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2.5">
        <i class="bi bi-info-circle-fill shrink-0"></i>{{ session('info') }}
    </div>
@endif

{{-- Hero header --}}
<div class="rounded-2xl shadow-sm overflow-hidden">
    <div class="bg-gradient-to-r from-primary to-primary/70 px-6 py-5 relative">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="relative flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white text-xl font-bold shrink-0 border border-white/30">
                    {{ strtoupper(substr(auth()->user()->firstname ?? auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-lg font-bold text-white leading-tight">
                            {{ auth()->user()->firstname ?? auth()->user()->name ?? __('Affilié') }}
                        </h2>
                        @if($affiliate->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Actif') }}
                            </span>
                        @elseif($affiliate->status === 'inactive')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-400/30 text-yellow-100 border border-yellow-300/30">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('En attente') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-400/30 text-red-100 border border-red-300/30">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Suspendu') }}
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-white/70 mt-0.5">
                        {{ __('Commission :') }}
                        <span class="font-semibold text-white">{{ number_format($affiliate->commission_rate, ($affiliate->commission_type ?? 'percent') === 'fixed' ? 2 : 0) }}{{ ($affiliate->commission_type ?? 'percent') === 'fixed' ? ' ' . setting('currency_symbol', '€') : '%' }}</span>
                        <span class="mx-1.5 text-white/40">·</span>
                        {{ __('Code :') }} <span class="font-mono font-semibold text-white">{{ $affiliate->referral_code }}</span>
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('affiliation.commissions') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-white/15 text-white border border-white/25 hover:bg-white/25 transition-colors">
                    <i class="bi bi-cash-stack text-xs"></i>{{ __('Commissions') }}
                </a>
                <a href="{{ route('affiliation.referrals') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-white/15 text-white border border-white/25 hover:bg-white/25 transition-colors">
                    <i class="bi bi-people text-xs"></i>{{ __('Parrainages') }}
                </a>
                <a href="{{ route('affiliation.settings') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-white/15 text-white border border-white/25 hover:bg-white/25 transition-colors">
                    <i class="bi bi-gear text-xs"></i>{{ __('Paramètres') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Lien de parrainage --}}
<div class="rounded-2xl bg-gradient-to-br from-primary/8 to-primary/3 dark:from-primary/15 dark:to-primary/5 border border-primary/20 dark:border-primary/25 p-5">
    <div class="flex items-center gap-2 mb-3">
        <div class="h-8 w-8 rounded-xl bg-primary/15 flex items-center justify-center shrink-0">
            <i class="bi bi-link-45deg text-primary text-sm"></i>
        </div>
        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Affilix::affiliation.referral_link') }}</p>
        @if($clickRate !== null)
            <span class="ml-auto text-xs text-primary font-medium flex items-center gap-1">
                <i class="bi bi-cursor-fill text-[11px]"></i>
                +{{ number_format($clickRate, 2) }} {{ setting('currency_symbol', '€') }} {{ __('/ clic unique') }}
            </span>
        @endif
    </div>
    <div class="flex gap-2">
        <input type="text" id="referral-link" value="{{ $affiliate->getReferralUrl() }}" readonly
            class="flex-1 rounded-xl border border-primary/25 bg-white/70 dark:bg-gray-800/70 text-gray-700 dark:text-gray-300 px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-primary/30 truncate">
        <button onclick="copyLink(this)"
            class="btn btn-primary rounded-xl shrink-0 flex items-center gap-2 px-4">
            <i class="bi bi-clipboard text-sm"></i>
            <span class="hidden sm:inline">{{ __('Copier') }}</span>
        </button>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-cursor-fill text-blue-600 dark:text-blue-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ number_format($stats['unique_clicks']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5 truncate">{{ __('Clics uniques') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-people-fill text-purple-600 dark:text-purple-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-bold tracking-tight text-purple-600 dark:text-purple-400">{{ number_format($stats['total_referrals']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5 truncate">{{ __('Parrainages') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-graph-up-arrow text-orange-600 dark:text-orange-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-bold tracking-tight text-orange-600 dark:text-orange-400">{{ number_format($stats['conversion_rate'], 1) }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5 truncate">{{ __('Conversion') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-cash-coin text-green-600 dark:text-green-400"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ number_format($stats['total_earnings'], 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5 truncate">{{ __('Gains totaux') }} {{ setting('currency_symbol', '€') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Solde + demande de paiement --}}
@php
    $pendingWithdrawal = $affiliate->withdrawals()->where('status', 'pending')->first();
    $minPayout = (float) setting('minimum_payout', 0);
    $approvedBalance = (float) $stats['pending_earnings'];
    $canRequest = !$pendingWithdrawal && $approvedBalance > 0 && $approvedBalance >= $minPayout;
@endphp
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    {{-- Solde disponible --}}
    <div class="sm:col-span-2 rounded-2xl bg-gradient-to-br from-gray-900 to-gray-700 dark:from-gray-700 dark:to-gray-800 p-5 text-white shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 rounded-full bg-white/5 -mr-8 -mt-8"></div>
        <div class="absolute bottom-0 left-0 w-20 h-20 rounded-full bg-white/5 -ml-6 -mb-6"></div>
        <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-1 relative">{{ __('Solde disponible') }}</p>
        <p class="text-4xl font-bold tracking-tight relative">{{ number_format($approvedBalance, 2) }} <span class="text-2xl text-gray-300">{{ setting('currency_symbol', '€') }}</span></p>
        <div class="mt-4 flex items-center gap-3 flex-wrap relative">
            @if($pendingWithdrawal)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-400/20 text-yellow-300 border border-yellow-400/30">
                    <i class="bi bi-hourglass-split text-[11px]"></i>
                    {{ __('En cours') }} — {{ number_format($pendingWithdrawal->amount, 2) }} {{ setting('currency_symbol', '€') }}
                </span>
            @elseif($canRequest)
                <form method="POST" action="{{ route('affiliation.withdraw') }}" onsubmit="return confirm('{{ __('Confirmer la demande de') }} {{ number_format($approvedBalance, 2) }} {{ setting('currency_symbol', '€') }} ?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-sm font-semibold bg-white text-gray-900 hover:bg-gray-100 transition-colors">
                        <i class="bi bi-send text-sm"></i>{{ __('Demander le paiement') }}
                    </button>
                </form>
            @elseif($approvedBalance < $minPayout && $minPayout > 0)
                <p class="text-xs text-gray-400">
                    {{ __('Encore') }} <span class="font-semibold text-white">{{ number_format($minPayout - $approvedBalance, 2) }} {{ setting('currency_symbol', '€') }}</span> {{ __('avant de pouvoir retirer') }}
                </p>
            @else
                <p class="text-xs text-gray-400">{{ __('Aucun solde approuvé') }}</p>
            @endif
            <a href="{{ route('affiliation.withdrawals') }}" class="text-xs text-gray-400 hover:text-white transition-colors ml-auto flex items-center gap-1">
                <i class="bi bi-clock-history text-[11px]"></i>{{ __('Historique') }}
            </a>
        </div>
    </div>

    {{-- Gains détaillés --}}
    <div class="space-y-3">
        <div class="card shadow-sm">
            <div class="card-body flex items-center gap-3 py-3">
                <div class="h-9 w-9 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-check-circle-fill text-green-600 dark:text-green-400 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold tracking-tight text-green-600 dark:text-green-400">{{ number_format($stats['paid_earnings'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('Payé') }}</p>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body flex items-center gap-3 py-3">
                <div class="h-9 w-9 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold tracking-tight text-yellow-600 dark:text-yellow-400">{{ number_format($stats['pending_earnings'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('En attente') }}</p>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body flex items-center gap-3 py-3">
                <div class="h-9 w-9 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-trophy-fill text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ number_format($stats['successful_referrals']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('Conversions') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="card shadow-sm">
        <div class="card-heading">
            <div class="flex items-center gap-2">
                <div class="h-7 w-7 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-cursor text-blue-600 dark:text-blue-400 text-xs"></i>
                </div>
                <h4 class="text-sm">{{ __('Clics — 8 semaines') }}</h4>
            </div>
        </div>
        <div class="card-body pt-2">
            <canvas id="client-clicks-chart" height="130"></canvas>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-heading">
            <div class="flex items-center gap-2">
                <div class="h-7 w-7 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-graph-up text-green-600 dark:text-green-400 text-xs"></i>
                </div>
                <h4 class="text-sm">{{ __('Gains — 6 mois') }}</h4>
            </div>
        </div>
        <div class="card-body pt-2">
            <canvas id="client-commissions-chart" height="130"></canvas>
        </div>
    </div>
</div>

{{-- Dernières commissions --}}
<div class="card shadow-sm">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <div class="h-7 w-7 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <i class="bi bi-receipt text-gray-500 dark:text-gray-400 text-xs"></i>
            </div>
            <h4 class="text-sm">{{ __('Dernières commissions') }}</h4>
        </div>
        <a href="{{ route('affiliation.commissions') }}" class="btn btn-secondary btn-sm ml-auto">
            {{ __('Voir tout') }}<i class="bi bi-arrow-right ml-1 text-xs"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="table w-full">
            <thead>
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Montant') }}</th>
                    <th class="px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentCommissions as $commission)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $commission->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $commission->description }}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap text-gray-900 dark:text-white">+{{ number_format($commission->amount, 2) }} {{ setting('currency_symbol', '€') }}</td>
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
                    <td colspan="4" class="py-14 text-center">
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
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
function copyLink(btn) {
    const val = document.getElementById('referral-link').value;
    navigator.clipboard.writeText(val).catch(() => {
        const el = document.getElementById('referral-link');
        el.select();
        document.execCommand('copy');
    });
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-clipboard-check text-sm"></i><span class="hidden sm:inline">{{ __("Copié !") }}</span>';
    setTimeout(() => btn.innerHTML = orig, 2000);
}

(function () {
    const dark      = document.documentElement.classList.contains('dark');
    const textColor = dark ? '#9ca3af' : '#6b7280';
    const gridColor = dark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
    const baseOpts  = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor } },
            y: { ticks: { color: textColor, font: { size: 11 } }, grid: { color: gridColor }, beginAtZero: true },
        },
    };

    const clicksData = @json($clicksChart);
    new Chart(document.getElementById('client-clicks-chart'), {
        type: 'bar',
        data: {
            labels: clicksData.map(d => d.label),
            datasets: [{ data: clicksData.map(d => d.count), backgroundColor: 'rgba(59,130,246,0.6)', borderColor: 'rgba(59,130,246,1)', borderWidth: 1.5, borderRadius: 6 }],
        },
        options: baseOpts,
    });

    const commData = @json($commissionsChart);
    new Chart(document.getElementById('client-commissions-chart'), {
        type: 'bar',
        data: {
            labels: commData.map(d => d.label),
            datasets: [{ data: commData.map(d => d.total), backgroundColor: 'rgba(34,197,94,0.6)', borderColor: 'rgba(34,197,94,1)', borderWidth: 1.5, borderRadius: 6 }],
        },
        options: { ...baseOpts, scales: { ...baseOpts.scales, y: { ...baseOpts.scales.y, ticks: { ...baseOpts.scales.y.ticks, callback: v => v.toFixed(2) + ' {{ setting('currency_symbol', '€') }}' } } } },
    });
})();
</script>
@endsection
