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
    <div class="card" style="border-left: 4px solid #6b7280;">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Addons\Affiliation\Models\Affiliate::count() }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide font-medium">{{ __('Affilix::affiliation.admin.total_affiliates') }}</p>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #22c55e;">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ \App\Addons\Affiliation\Models\Affiliate::where('status', 'active')->count() }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide font-medium">{{ __('Affilix::affiliation.admin.active_affiliates') }}</p>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #3b82f6;">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format(\App\Addons\Affiliation\Models\AffiliateCommission::sum('amount'), 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide font-medium">{{ __('Affilix::affiliation.admin.total_commissions') }}</p>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #eab308;">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format(\App\Addons\Affiliation\Models\AffiliateCommission::where('status', 'pending')->sum('amount'), 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide font-medium">{{ __('Affilix::affiliation.pending') }}</p>
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
            <div class="flex items-center justify-between gap-6 px-5 py-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Taux de commission par défaut') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Appliqué aux nouveaux affiliés. Modifiable individuellement sur chaque fiche.') }}</p>
                </div>
                <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 shrink-0">
                    <input type="number" name="default_commission_rate"
                        value="{{ setting('default_commission_rate', 10) }}"
                        min="0" max="100" step="0.1"
                        class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-16 text-right">
                    <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">%</span>
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
