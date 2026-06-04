@extends('admin.layouts.admin')

@section('title', __('Tableau de bord Affiliation'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap justify-between items-start gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="bi bi-graph-up-arrow text-primary"></i>{{ __('Tableau de bord') }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Vue globale du programme d\'affiliation') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-people mr-1"></i>{{ __('Affiliés') }}
        </a>
        <a href="{{ route('affiliation.admin.commissions') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-cash-stack mr-1"></i>{{ __('Commissions') }}
        </a>
        <a href="{{ route('affiliation.admin.settings') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-gear mr-1"></i>{{ __('Paramètres') }}
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                <i class="bi bi-people text-gray-500 dark:text-gray-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $totalAffiliates }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Affiliés') }}</p>
                <p class="text-xs text-green-500 mt-0.5">{{ $activeAffiliates }} {{ __('actifs') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-cursor text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ number_format($totalClicks) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Clics uniques') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-arrow-repeat text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ $conversionRate }}%</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Taux de conversion') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-cash text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-purple-600 dark:text-purple-400">{{ number_format($totalPaid, 2) }} {{ $currency }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Total versé') }}</p>
                <p class="text-xs text-yellow-500 mt-0.5">{{ number_format($totalPending, 2) }} {{ $currency }} {{ __('att.') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

    {{-- Graphique commissions par mois --}}
    <div class="card lg:col-span-3">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <i class="bi bi-bar-chart-fill text-primary text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Commissions — 6 derniers mois') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Total généré vs versé') }}</p>
                </div>
            </div>
        </div>
        <div class="p-5">
            <canvas id="monthly-chart" height="180"></canvas>
        </div>
    </div>

    {{-- Top 5 affiliés --}}
    <div class="card lg:col-span-2">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-trophy-fill text-yellow-500 text-sm"></i>
                </div>
                <h4 class="leading-none">{{ __('Top 5 affiliés') }}</h4>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($topAffiliates as $i => $affiliate)
            <div class="flex items-center gap-3 px-4 py-3">
                <span class="text-sm font-bold w-5 text-center {{ $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : ($i === 2 ? 'text-amber-600' : 'text-gray-300 dark:text-gray-600')) }}">
                    {{ $i + 1 }}
                </span>
                <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-xs font-bold text-primary">
                    {{ strtoupper(substr($affiliate->customer->firstname ?? $affiliate->customer->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <a href="{{ route('affiliation.admin.show', $affiliate) }}"
                        class="text-sm font-medium text-gray-900 dark:text-white hover:text-primary truncate block">
                        {{ $affiliate->customer->firstname ?? '' }} {{ $affiliate->customer->lastname ?? ($affiliate->customer->name ?? '—') }}
                    </a>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $affiliate->successful_referrals }} {{ __('conv.') }} · {{ $affiliate->unique_clicks }} {{ __('clics') }}</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($affiliate->total_earnings, 2) }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $currency }}</p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center gap-2 py-10">
                <i class="bi bi-trophy text-3xl text-gray-200 dark:text-gray-700"></i>
                <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucun affilié') }}</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- Moyens de paiement --}}
@php
    $methodConfig = [
        'balance'       => ['label' => 'Balance',         'icon' => 'bi-wallet2',  'color' => '#6366f1'],
        'paypal'        => ['label' => 'PayPal',          'icon' => 'bi-paypal',   'color' => '#3b82f6'],
        'bank_transfer' => ['label' => 'Virement bancaire','icon' => 'bi-bank',    'color' => '#22c55e'],
    ];
    $totalMethodAffiliates = $paymentMethods->sum();
@endphp
<div class="card mt-4">
    <div class="card-heading">
        <div class="flex items-center gap-2.5">
            <div class="h-8 w-8 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center shrink-0">
                <i class="bi bi-credit-card-2-front text-green-600 dark:text-green-400 text-sm"></i>
            </div>
            <h4 class="leading-none">{{ __('Méthodes de paiement') }}</h4>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100 dark:divide-gray-700">
        @foreach($methodConfig as $key => $cfg)
        @php $count = $paymentMethods->get($key, 0); $pct = $totalMethodAffiliates > 0 ? round(($count / $totalMethodAffiliates) * 100) : 0; @endphp
        <div class="px-6 py-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="h-9 w-9 rounded-lg flex items-center justify-center shrink-0" style="background: {{ $cfg['color'] }}22;">
                    <i class="bi {{ $cfg['icon'] }}" style="color: {{ $cfg['color'] }};"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $cfg['label'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $count }} {{ __('affilié(s)') }}</p>
                </div>
                <span class="ml-auto text-lg font-bold" style="color: {{ $cfg['color'] }};">{{ $pct }}%</span>
            </div>
            <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all" style="width: {{ $pct }}%; background: {{ $cfg['color'] }};"></div>
            </div>
        </div>
        @endforeach
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
(function () {
    const isDark  = document.documentElement.classList.contains('dark');
    const grid    = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const labels  = @json($monthlyChart->pluck('label'));
    const totals  = @json($monthlyChart->pluck('total'));
    const paid    = @json($monthlyChart->pluck('paid'));

    new Chart(document.getElementById('monthly-chart'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: '{{ __('Total généré') }}',
                    data: totals,
                    backgroundColor: 'rgba(99,102,241,0.2)',
                    borderColor: 'rgba(99,102,241,0.8)',
                    borderWidth: 2,
                    borderRadius: 6,
                },
                {
                    label: '{{ __('Versé') }}',
                    data: paid,
                    backgroundColor: 'rgba(34,197,94,0.25)',
                    borderColor: 'rgba(34,197,94,0.8)',
                    borderWidth: 2,
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 16 } } },
            scales: {
                x: { grid: { color: grid } },
                y: { grid: { color: grid }, beginAtZero: true, ticks: { callback: v => v + ' {{ $currency }}' } },
            },
        },
    });
})();
</script>
@endsection
