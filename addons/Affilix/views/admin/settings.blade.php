@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.settings'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap justify-between items-start gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="bi bi-gear-fill text-primary"></i>{{ __('Affilix::affiliation.settings') }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Configurez le comportement global du programme d\'affiliation') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-people mr-1"></i>{{ __('Affilix::affiliation.admin.affiliates') }}
        </a>
        <a href="{{ route('affiliation.admin.commissions') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-cash-stack mr-1"></i>{{ __('Affilix::affiliation.commissions') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-5 text-sm text-green-700 dark:text-green-300 flex items-center gap-2.5">
        <i class="bi bi-check-circle-fill text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center shrink-0">
                <i class="bi bi-people text-gray-500 dark:text-gray-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ \App\Addons\Affiliation\Models\Affiliate::count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Affilix::affiliation.admin.total_affiliates') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ \App\Addons\Affiliation\Models\Affiliate::where('status', 'active')->count() }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Affilix::affiliation.admin.active_affiliates') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-cash text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-blue-600 dark:text-blue-400">{{ number_format(\App\Addons\Affiliation\Models\AffiliateCommission::sum('amount'), 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Affilix::affiliation.admin.total_commissions') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-yellow-500 dark:text-yellow-400">{{ number_format(\App\Addons\Affiliation\Models\AffiliateCommission::where('status', 'pending')->sum('amount'), 2) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Affilix::affiliation.pending') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Formulaire --}}
<form method="POST" action="{{ route('affiliation.admin.settings.update') }}">
    @csrf
    @method('PUT')

    {{-- Section : Paramètres généraux --}}
    <div class="card mb-4">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <i class="bi bi-sliders text-primary text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Paramètres généraux') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Taux, seuil de paiement et durée du cookie') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @php $defCommType = \App\Addons\Affiliation\Models\AffiliationSetting::get('default_commission_type', 'percent'); @endphp
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Commission par défaut') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Appliquée aux nouveaux affiliés. Modifiable individuellement sur chaque fiche.') }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button type="button" onclick="setDefCommType('percent')" id="def-btn-percent"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ $defCommType === 'percent' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">
                            %
                        </button>
                        <button type="button" onclick="setDefCommType('fixed')" id="def-btn-fixed"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ $defCommType === 'fixed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ setting('currency_symbol', '€') }}
                        </button>
                    </div>
                    <input type="hidden" name="default_commission_type" id="default_commission_type" value="{{ $defCommType }}">
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9">
                        <input type="number" name="default_commission_rate"
                            value="{{ setting('default_commission_rate', 10) }}"
                            min="0" max="99999" step="0.01"
                            class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-16 text-right">
                        <span id="def-commission-unit" class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ $defCommType === 'fixed' ? setting('currency_symbol', '€') : '%' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Seuil minimum de paiement') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Un affilié ne peut demander un versement qu\'une fois ce montant atteint.') }}</p>
                </div>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 shrink-0">
                    <input type="number" name="minimum_payout"
                        value="{{ setting('minimum_payout', 50) }}"
                        min="0" step="0.01"
                        class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-16 text-right">
                    <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ setting('currency_symbol', '€') }}</span>
                </div>
            </div>
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Durée du cookie de parrainage') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Fenêtre pendant laquelle un visiteur est attribué à l\'affilié après avoir cliqué sur son lien.') }}</p>
                </div>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 shrink-0">
                    <input type="number" name="cookie_lifetime"
                        value="{{ setting('cookie_lifetime', 30) }}"
                        min="1" max="365"
                        class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-16 text-right">
                    <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ __('jours') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Section : Paliers de commission --}}
    @php
        $tiersEnabled = \App\Addons\Affiliation\Models\AffiliationSetting::get('commission_tiers_enabled', '0') === '1';
        $tiersMetric  = \App\Addons\Affiliation\Models\AffiliationSetting::get('commission_tiers_metric', 'successful_referrals');
        $tiersData    = json_decode(\App\Addons\Affiliation\Models\AffiliationSetting::get('commission_tiers', '[]'), true) ?: [];
        $metricLabels = ['successful_referrals' => 'conversions réussies', 'total_referrals' => 'parrainages', 'unique_clicks' => 'clics uniques'];
    @endphp
    <div class="card mb-4">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-bar-chart-steps text-teal-600 dark:text-teal-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Paliers de commission') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Augmentez ou réduisez automatiquement le taux selon les performances de l\'affilié') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">

            {{-- Toggle --}}
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Activer les paliers') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Le taux de l\'affilié évolue automatiquement selon ses résultats.') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="commission_tiers_enabled" value="1" id="tiers-toggle" class="sr-only peer"
                        {{ $tiersEnabled ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>

            {{-- Panneau --}}
            <div id="tiers-panel" class="{{ $tiersEnabled ? '' : 'hidden' }} divide-y divide-gray-100 dark:divide-gray-700">

                {{-- Métrique --}}
                <div class="flex items-center justify-between gap-6 px-5 py-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Basé sur') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Critère utilisé pour évaluer le palier de l\'affilié.') }}</p>
                    </div>
                    <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1 shrink-0">
                        @foreach(['successful_referrals' => 'Conversions', 'total_referrals' => 'Parrainages', 'unique_clicks' => 'Clics'] as $val => $lbl)
                        <label class="cursor-pointer">
                            <input type="radio" name="commission_tiers_metric" value="{{ $val }}" class="sr-only tiers-metric-radio"
                                {{ $tiersMetric === $val ? 'checked' : '' }}>
                            <span class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors block
                                {{ $tiersMetric === $val ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700' }}">
                                {{ __($lbl) }}
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Table des paliers --}}
                <div class="px-5 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Paliers') }}</p>
                        <button type="button" onclick="addTier()" class="btn btn-secondary btn-sm">
                            <i class="bi bi-plus mr-1"></i>{{ __('Ajouter un palier') }}
                        </button>
                    </div>
                    <div class="rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('À partir de') }} <span id="metric-label" class="text-primary">({{ $metricLabels[$tiersMetric] ?? '' }})</span></th>
                                    <th class="px-4 py-2.5 text-left text-xs font-medium text-gray-500 dark:text-gray-400">{{ __('Taux') }}</th>
                                    <th class="px-4 py-2.5 w-10"></th>
                                </tr>
                            </thead>
                            <tbody id="tiers-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($tiersData as $tier)
                                <tr class="tier-row bg-white dark:bg-gray-800">
                                    <td class="px-4 py-2.5">
                                        <input type="number" name="tiers_threshold[]" value="{{ $tier['threshold'] }}" min="0" required
                                            class="w-24 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1 text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-primary">
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-center gap-2">
                                            <div class="flex gap-0.5 bg-gray-100 dark:bg-gray-700 rounded-md p-0.5">
                                                <button type="button" onclick="setTierType(this,'percent')"
                                                    class="tier-type-btn px-2 py-1 rounded text-xs font-medium transition-all {{ ($tier['type'] ?? 'percent') === 'percent' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}"
                                                    data-type="percent">%</button>
                                                <button type="button" onclick="setTierType(this,'fixed')"
                                                    class="tier-type-btn px-2 py-1 rounded text-xs font-medium transition-all {{ ($tier['type'] ?? 'percent') === 'fixed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}"
                                                    data-type="fixed">{{ setting('currency_symbol', '€') }}</button>
                                            </div>
                                            <input type="hidden" name="tiers_type[]" value="{{ $tier['type'] ?? 'percent' }}" class="tier-type-input">
                                            <input type="number" name="tiers_rate[]" value="{{ $tier['rate'] }}" min="0" max="99999" step="0.01" required
                                                class="w-24 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1 text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-primary">
                                        </div>
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <button type="button" onclick="this.closest('tr').remove()" class="text-red-400 hover:text-red-600 transition-colors">
                                            <i class="bi bi-trash text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr id="tiers-empty" class="bg-white dark:bg-gray-800">
                                    <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-400 dark:text-gray-500">
                                        {{ __('Aucun palier. Cliquez sur "Ajouter un palier".') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                        <i class="bi bi-info-circle mr-1"></i>{{ __('Exemple : palier "0 → 10%" + palier "5 → 15%" = après 5 conversions, le taux passe à 15%.') }}
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- Section : Commission premier achat --}}
    @php
        $foEnabled   = \App\Addons\Affiliation\Models\AffiliationSetting::get('first_order_commission_enabled', '0') === '1';
        $foType      = \App\Addons\Affiliation\Models\AffiliationSetting::get('first_order_commission_type', 'percent');
        $foRate      = \App\Addons\Affiliation\Models\AffiliationSetting::get('first_order_commission_rate', '0');
        $afoType     = \App\Addons\Affiliation\Models\AffiliationSetting::get('after_first_order_commission_type', 'percent');
        $afoRate     = \App\Addons\Affiliation\Models\AffiliationSetting::get('after_first_order_commission_rate', '0');
    @endphp
    <div class="card mb-4">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-1-circle text-indigo-600 dark:text-indigo-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Commission — Premier achat') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Taux distincts pour le 1er achat et les suivants. Mettre 0 = aucune commission.') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">

            {{-- Toggle --}}
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Activer des taux distincts') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Si désactivé, le taux habituel s\'applique à toutes les commandes.') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="first_order_commission_enabled" value="1" id="fo-toggle" class="sr-only peer"
                        {{ $foEnabled ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>

            <div id="fo-panel" class="{{ $foEnabled ? '' : 'hidden' }} divide-y divide-gray-100 dark:divide-gray-700">

                {{-- Taux 1er achat --}}
                <div class="flex items-center justify-between gap-6 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Taux — 1er achat') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Commission appliquée sur la première commande du filleul.') }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                            <button type="button" onclick="setFoType('percent')" id="fo-btn-percent"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ $foType === 'percent' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">%</button>
                            <button type="button" onclick="setFoType('fixed')" id="fo-btn-fixed"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ $foType === 'fixed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">{{ setting('currency_symbol', '€') }}</button>
                        </div>
                        <input type="hidden" name="first_order_commission_type" id="first_order_commission_type" value="{{ $foType }}">
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9">
                            <input type="number" name="first_order_commission_rate"
                                value="{{ $foRate }}" min="0" max="99999" step="0.01"
                                class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-16 text-right">
                            <span id="fo-unit" class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ $foType === 'fixed' ? setting('currency_symbol', '€') : '%' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Taux achats suivants --}}
                <div class="flex items-center justify-between gap-6 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Taux — Achats suivants') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Commission pour les 2e achat et au-delà. Mettre 0 pour ne verser aucune commission après le 1er achat.') }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                            <button type="button" onclick="setAfoType('percent')" id="afo-btn-percent"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ $afoType === 'percent' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">%</button>
                            <button type="button" onclick="setAfoType('fixed')" id="afo-btn-fixed"
                                class="px-3 py-1.5 rounded-md text-xs font-medium transition-all {{ $afoType === 'fixed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">{{ setting('currency_symbol', '€') }}</button>
                        </div>
                        <input type="hidden" name="after_first_order_commission_type" id="after_first_order_commission_type" value="{{ $afoType }}">
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9">
                            <input type="number" name="after_first_order_commission_rate"
                                value="{{ $afoRate }}" min="0" max="99999" step="0.01"
                                class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-16 text-right">
                            <span id="afo-unit" class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ $afoType === 'fixed' ? setting('currency_symbol', '€') : '%' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Section : Inscription --}}
    @php
        $registrationEnabled = \App\Addons\Affiliation\Models\AffiliationSetting::get('registration_enabled', '1') === '1';
        $registrationMessage = \App\Addons\Affiliation\Models\AffiliationSetting::get('registration_disabled_message', '');
    @endphp
    <div class="card mb-4">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-person-plus text-orange-600 dark:text-orange-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Inscription') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Contrôlez l\'accès au formulaire d\'inscription affilié') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Inscription libre') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Si désactivé, les clients voient un message à la place du formulaire.') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="registration_enabled" value="1" id="registration-toggle" class="sr-only peer"
                        {{ $registrationEnabled ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
            <div id="registration-message-panel" class="{{ $registrationEnabled ? 'hidden' : '' }} px-5 py-4">
                <p class="text-sm font-medium text-gray-900 dark:text-white mb-2">{{ __('Message affiché') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">{{ __('Laissez vide pour le message par défaut. Vous pouvez indiquer comment contacter le support.') }}</p>
                <textarea name="registration_disabled_message" rows="3"
                    placeholder="{{ __('Pour rejoindre notre programme d\'affiliation, veuillez ouvrir un ticket ou contacter le support.') }}"
                    class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-sm text-gray-800 dark:text-gray-200 placeholder-gray-400 outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none transition">{{ $registrationMessage }}</textarea>
            </div>
        </div>
    </div>

    {{-- Section : Approbations --}}
    <div class="card mb-4">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-shield-check text-blue-600 dark:text-blue-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Approbations') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Contrôlez la validation des affiliés et des commissions') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Approbation automatique des affiliés') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Si désactivé, chaque nouvelle inscription devra être approuvée manuellement depuis la liste des affiliés.') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="auto_approve" value="1" class="sr-only peer"
                        {{ setting('auto_approve', '1') == '1' ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Approbation automatique des commissions') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Si désactivé, chaque commission passe en attente et doit être approuvée avant d\'être payable.') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="auto_approve_commissions" value="1" class="sr-only peer"
                        {{ setting('auto_approve_commissions', '0') == '1' ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Commission sur la première commande uniquement') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Affilix::affiliation.settings_first_order_only_help') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="commission_first_order_only" value="1" class="sr-only peer"
                        {{ setting('commission_first_order_only', '0') == '1' ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Section : Rémunération par clic --}}
    <div class="card mb-4">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-cursor-fill text-purple-600 dark:text-purple-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Rémunération par clic') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Attribuez un montant fixe à chaque clic unique sur un lien d\'affiliation') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Activer la rémunération par clic') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Affilix::affiliation.settings_click_remuneration_help') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="click_remuneration_enabled" value="1" class="sr-only peer"
                        {{ \App\Addons\Affiliation\Models\AffiliationSetting::get('click_remuneration_enabled', '0') === '1' ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Montant par clic unique') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Affilix::affiliation.settings_click_remuneration_rate_help') }}</p>
                </div>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 shrink-0">
                    <input type="number" name="click_remuneration_rate"
                        value="{{ \App\Addons\Affiliation\Models\AffiliationSetting::get('click_remuneration_rate', '0.00') }}"
                        min="0" step="0.001"
                        class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-20 text-right">
                    <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ setting('currency_symbol', '€') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Section : Méthodes de paiement --}}
    <div class="card mb-6">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-wallet2 text-green-600 dark:text-green-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Méthodes de paiement autorisées') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Cochez les modes de versement que vos affiliés peuvent choisir') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                        <i class="bi bi-wallet2 text-gray-500 dark:text-gray-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Balance') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Crédit ajouté directement sur le compte client.') }}</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="affiliation_payment_balance" value="1" class="sr-only peer"
                        {{ setting('affiliation_payment_balance', '1') == '1' ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                        <i class="bi bi-paypal text-blue-500 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">PayPal</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Versement via l\'adresse email PayPal de l\'affilié.') }}</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="affiliation_payment_paypal" value="1" class="sr-only peer"
                        {{ setting('affiliation_payment_paypal', '1') == '1' ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="h-9 w-9 rounded-lg bg-green-50 dark:bg-green-900/20 flex items-center justify-center shrink-0">
                        <i class="bi bi-bank text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Virement bancaire') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Versement sur le compte bancaire (IBAN) renseigné par l\'affilié.') }}</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="affiliation_payment_bank_transfer" value="1" class="sr-only peer"
                        {{ setting('affiliation_payment_bank_transfer', '1') == '1' ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>
        </div>
    </div>

    {{-- Section : Paiement automatique --}}
    @php
        $autoPayEnabled   = \App\Addons\Affiliation\Models\AffiliationSetting::get('auto_payment_enabled', '0') === '1';
        $autoPayFrequency = \App\Addons\Affiliation\Models\AffiliationSetting::get('auto_payment_frequency', 'monthly');
        $autoPayThreshold = \App\Addons\Affiliation\Models\AffiliationSetting::get('auto_payment_threshold', '1.00');
    @endphp
    <div class="card mb-6">
        <div class="card-heading">
            <div class="flex items-center gap-2.5">
                <div class="h-8 w-8 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center shrink-0">
                    <i class="bi bi-calendar-check text-yellow-600 dark:text-yellow-400 text-sm"></i>
                </div>
                <div>
                    <h4 class="leading-none">{{ __('Paiement automatique') }}</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Versement automatique sur le solde — balance uniquement') }}</p>
                </div>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            {{-- Toggle ON/OFF --}}
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Activer le paiement automatique') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Déclenche automatiquement un virement sur le solde des affiliés éligibles.') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="auto_payment_enabled" value="1" id="auto-pay-toggle" class="sr-only peer"
                        {{ $autoPayEnabled ? 'checked' : '' }}>
                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                        peer-checked:bg-primary
                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                        peer-checked:after:translate-x-[18px]"></div>
                </label>
            </div>

            {{-- Panneau sous-paramètres --}}
            <div id="auto-pay-panel" class="{{ $autoPayEnabled ? '' : 'hidden' }} divide-y divide-gray-100 dark:divide-gray-700">

                {{-- Fréquence --}}
                <div class="flex items-start justify-between gap-6 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Fréquence') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Quotidien : chaque jour à 06h00. Mensuel : le 1er de chaque mois.') }}</p>
                    </div>
                    <div class="flex gap-2 shrink-0">
                        @foreach(['daily' => ['icon' => 'bi-sunrise', 'label' => 'Quotidien'], 'monthly' => ['icon' => 'bi-calendar3', 'label' => 'Mensuel']] as $val => $opt)
                        <label class="cursor-pointer" onclick="selectFreq('{{ $val }}')">
                            <input type="radio" name="auto_payment_frequency" value="{{ $val }}"
                                id="freq-{{ $val }}" class="sr-only"
                                {{ $autoPayFrequency === $val ? 'checked' : '' }}>
                            <div id="freq-btn-{{ $val }}"
                                class="px-3 py-1.5 rounded-lg text-xs font-medium border-2 transition-all select-none cursor-pointer
                                {{ $autoPayFrequency === $val
                                    ? 'border-primary bg-primary/10 text-primary'
                                    : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400' }}">
                                <i class="bi {{ $opt['icon'] }} mr-1"></i>{{ __($opt['label']) }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- Seuil --}}
                <div class="flex items-center justify-between gap-6 px-5 py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Seuil de déclenchement') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Montant minimum approuvé requis pour déclencher le paiement automatique.') }}</p>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 shrink-0">
                        <input type="number" name="auto_payment_threshold"
                            value="{{ $autoPayThreshold }}"
                            min="0.01" step="0.01"
                            class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-20 text-right">
                        <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ setting('currency_symbol', '€') }}</span>
                    </div>
                </div>

                {{-- Info --}}
                <div class="px-5 py-3 bg-blue-50 dark:bg-blue-900/10 flex items-center gap-2">
                    <i class="bi bi-info-circle text-blue-500 dark:text-blue-400 text-sm shrink-0"></i>
                    <p class="text-xs text-blue-600 dark:text-blue-400">
                        {{ __('Seuls les affiliés actifs avec la méthode de paiement "Solde" (balance) et sans demande en cours sont traités.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-lg mr-1.5"></i>{{ __('global.save') }}
        </button>
    </div>
</form>

<script>
function setDefCommType(type) {
    document.getElementById('default_commission_type').value = type;
    document.getElementById('def-commission-unit').textContent = type === 'percent' ? '%' : '{{ setting('currency_symbol', '€') }}';
    var active   = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
    var inactive = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all text-gray-500 dark:text-gray-400';
    document.getElementById('def-btn-percent').className = type === 'percent' ? active : inactive;
    document.getElementById('def-btn-fixed').className   = type === 'fixed'   ? active : inactive;
}

// Paliers
document.getElementById('tiers-toggle').addEventListener('change', function () {
    document.getElementById('tiers-panel').classList.toggle('hidden', !this.checked);
});

document.querySelectorAll('.tiers-metric-radio').forEach(function(r) {
    r.addEventListener('change', function() {
        var labels = {'successful_referrals':'conversions réussies','total_referrals':'parrainages','unique_clicks':'clics uniques'};
        var el = document.getElementById('metric-label');
        if (el) el.textContent = '(' + (labels[this.value] || this.value) + ')';
        document.querySelectorAll('.tiers-metric-radio').forEach(function(radio) {
            var span = radio.nextElementSibling;
            if (radio.checked) {
                span.className = 'px-3 py-1.5 text-xs font-medium rounded-md transition-colors block bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
            } else {
                span.className = 'px-3 py-1.5 text-xs font-medium rounded-md transition-colors block text-gray-500 dark:text-gray-400 hover:text-gray-700';
            }
        });
    });
});

function setTierType(btn, type) {
    var row = btn.closest('tr');
    row.querySelector('.tier-type-input').value = type;
    row.querySelectorAll('.tier-type-btn').forEach(function(b) {
        b.className = b.dataset.type === type
            ? 'tier-type-btn px-2 py-1 rounded text-xs font-medium transition-all bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm'
            : 'tier-type-btn px-2 py-1 rounded text-xs font-medium transition-all text-gray-500 dark:text-gray-400';
    });
}

function addTier() {
    var empty = document.getElementById('tiers-empty');
    if (empty) empty.remove();
    var sym = '{{ setting('currency_symbol', '€') }}';
    var row = document.createElement('tr');
    row.className = 'tier-row bg-white dark:bg-gray-800';
    row.innerHTML = '<td class="px-4 py-2.5"><input type="number" name="tiers_threshold[]" value="0" min="0" required class="w-24 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1 text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-primary"></td>'
        + '<td class="px-4 py-2.5"><div class="flex items-center gap-2"><div class="flex gap-0.5 bg-gray-100 dark:bg-gray-700 rounded-md p-0.5"><button type="button" onclick="setTierType(this,\'percent\')" class="tier-type-btn px-2 py-1 rounded text-xs font-medium transition-all bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm" data-type="percent">%</button><button type="button" onclick="setTierType(this,\'fixed\')" class="tier-type-btn px-2 py-1 rounded text-xs font-medium transition-all text-gray-500 dark:text-gray-400" data-type="fixed">' + sym + '</button></div><input type="hidden" name="tiers_type[]" value="percent" class="tier-type-input"><input type="number" name="tiers_rate[]" value="0" min="0" max="99999" step="0.01" required class="w-24 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1 text-sm text-gray-900 dark:text-white outline-none focus:ring-1 focus:ring-primary"></div></td>'
        + '<td class="px-4 py-2.5 text-center"><button type="button" onclick="this.closest(\'tr\').remove()" class="text-red-400 hover:text-red-600 transition-colors"><i class="bi bi-trash text-sm"></i></button></td>';
    document.getElementById('tiers-body').appendChild(row);
}

document.getElementById('fo-toggle').addEventListener('change', function () {
    document.getElementById('fo-panel').classList.toggle('hidden', !this.checked);
});

function setFoType(type) {
    document.getElementById('first_order_commission_type').value = type;
    document.getElementById('fo-unit').textContent = type === 'percent' ? '%' : '{{ setting('currency_symbol', '€') }}';
    var active   = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
    var inactive = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all text-gray-500 dark:text-gray-400';
    document.getElementById('fo-btn-percent').className = type === 'percent' ? active : inactive;
    document.getElementById('fo-btn-fixed').className   = type === 'fixed'   ? active : inactive;
}

function setAfoType(type) {
    document.getElementById('after_first_order_commission_type').value = type;
    document.getElementById('afo-unit').textContent = type === 'percent' ? '%' : '{{ setting('currency_symbol', '€') }}';
    var active   = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
    var inactive = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all text-gray-500 dark:text-gray-400';
    document.getElementById('afo-btn-percent').className = type === 'percent' ? active : inactive;
    document.getElementById('afo-btn-fixed').className   = type === 'fixed'   ? active : inactive;
}

document.getElementById('registration-toggle').addEventListener('change', function () {
    document.getElementById('registration-message-panel').classList.toggle('hidden', this.checked);
});

document.getElementById('auto-pay-toggle').addEventListener('change', function () {
    document.getElementById('auto-pay-panel').classList.toggle('hidden', !this.checked);
});

function selectFreq(val) {
    ['daily', 'monthly'].forEach(function (v) {
        var btn = document.getElementById('freq-btn-' + v);
        var base = 'px-3 py-1.5 rounded-lg text-xs font-medium border-2 transition-all select-none cursor-pointer ';
        btn.className = base + (v === val
            ? 'border-primary bg-primary/10 text-primary'
            : 'border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400');
        document.getElementById('freq-' + v).checked = (v === val);
    });
}
</script>

</div>
@endsection
