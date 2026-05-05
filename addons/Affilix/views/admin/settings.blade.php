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
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    @include('shared/input', [
                        'name'  => 'default_commission_rate',
                        'label' => __('Taux de commission par défaut (%)'),
                        'value' => setting('default_commission_rate', 10),
                        'type'  => 'number',
                        'help'  => __('Pourcentage appliqué à chaque vente générée par un affilié'),
                    ])
                </div>
                <div>
                    @include('shared/input', [
                        'name'  => 'minimum_payout',
                        'label' => __('Montant minimum de paiement') . ' (' . setting('currency_symbol', '€') . ')',
                        'value' => setting('minimum_payout', 50),
                        'type'  => 'number',
                        'help'  => __('Seuil minimum avant qu\'un affilié puisse être payé'),
                    ])
                </div>
                <div>
                    @include('shared/input', [
                        'name'  => 'cookie_lifetime',
                        'label' => __('Durée du cookie de parrainage (jours)'),
                        'value' => setting('cookie_lifetime', 30),
                        'type'  => 'number',
                        'help'  => __('Nombre de jours pendant lesquels un clic sur le lien est mémorisé'),
                    ])
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
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    @include('shared/checkbox', [
                        'name'    => 'auto_approve',
                        'label'   => __('Approbation automatique des nouveaux affiliés'),
                        'value'   => '1',
                        'checked' => setting('auto_approve', '1') == '1',
                    ])
                </div>
                <div>
                    @include('shared/checkbox', [
                        'name'    => 'auto_approve_commissions',
                        'label'   => __('Approbation automatique des commissions'),
                        'value'   => '1',
                        'checked' => setting('auto_approve_commissions', '0') == '1',
                    ])
                </div>
                <div>
                    @include('shared/checkbox', [
                        'name'    => 'commission_first_order_only',
                        'label'   => __('Affilix::affiliation.settings_first_order_only'),
                        'value'   => '1',
                        'checked' => setting('commission_first_order_only', '0') == '1',
                        'help'    => __('Affilix::affiliation.settings_first_order_only_help'),
                    ])
                </div>
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
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    @include('shared/checkbox', [
                        'name'    => 'click_remuneration_enabled',
                        'label'   => __('Affilix::affiliation.settings_click_remuneration'),
                        'value'   => '1',
                        'checked' => setting('click_remuneration_enabled', '0') == '1',
                        'help'    => __('Affilix::affiliation.settings_click_remuneration_help'),
                    ])
                </div>
                <div>
                    @include('shared/input', [
                        'name'  => 'click_remuneration_rate',
                        'label' => __('Affilix::affiliation.settings_click_remuneration_rate') . ' (' . setting('currency_symbol', '€') . ')',
                        'value' => setting('click_remuneration_rate', '0.00'),
                        'type'  => 'number',
                        'step'  => '0.01',
                        'help'  => __('Affilix::affiliation.settings_click_remuneration_rate_help'),
                    ])
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
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 font-normal">{{ __('Modes de versement proposés à vos affiliés') }}</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div>
                    @include('shared/checkbox', [
                        'name'    => 'affiliation_payment_balance',
                        'label'   => __('Balance (Fonds du compte client)'),
                        'value'   => '1',
                        'checked' => setting('affiliation_payment_balance', '1') == '1',
                    ])
                </div>
                <div>
                    @include('shared/checkbox', [
                        'name'    => 'affiliation_payment_paypal',
                        'label'   => 'PayPal',
                        'value'   => '1',
                        'checked' => setting('affiliation_payment_paypal', '1') == '1',
                    ])
                </div>
                <div>
                    @include('shared/checkbox', [
                        'name'    => 'affiliation_payment_bank_transfer',
                        'label'   => __('Virement bancaire (IBAN)'),
                        'value'   => '1',
                        'checked' => setting('affiliation_payment_bank_transfer', '1') == '1',
                    ])
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

</div>
@endsection
