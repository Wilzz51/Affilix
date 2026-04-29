@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.settings'))

@section('content')

<div class="flex justify-between items-center mb-6 pt-4">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Affilix::affiliation.settings') }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary">
            <i class="bi bi-people mr-1"></i>{{ __('Affilix::affiliation.admin.affiliates') }}
        </a>
        <a href="{{ route('affiliation.admin.commissions') }}" class="btn btn-secondary">
            <i class="bi bi-cash-stack mr-1"></i>{{ __('Affilix::affiliation.commissions') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-6 text-sm text-green-700 dark:text-green-300">
        <i class="bi bi-check-circle mr-1"></i>{{ session('success') }}
    </div>
@endif

{{-- Vue d'ensemble --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ \App\Addons\Affiliation\Models\Affiliate::count() }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.admin.total_affiliates') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ \App\Addons\Affiliation\Models\Affiliate::where('status', 'active')->count() }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.admin.active_affiliates') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format(\App\Addons\Affiliation\Models\AffiliateCommission::sum('amount'), 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.admin.total_commissions') }}</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body text-center">
            <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format(\App\Addons\Affiliation\Models\AffiliateCommission::where('status', 'pending')->sum('amount'), 2) }} {{ setting('currency_symbol', '€') }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wide">{{ __('Affilix::affiliation.pending') }}</p>
        </div>
    </div>
</div>

{{-- Formulaire paramètres --}}
<div class="card">
    <div class="card-heading">
        <h4>{{ __('Affilix::affiliation.settings') }}</h4>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('affiliation.admin.settings.update') }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
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

            <hr class="my-4 border-gray-200 dark:border-gray-700">

            <h5 class="font-semibold text-gray-600 dark:text-gray-400 uppercase text-sm mb-3">{{ __('Approbations') }}</h5>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
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

            <hr class="my-4 border-gray-200 dark:border-gray-700">

            <h5 class="font-semibold text-gray-600 dark:text-gray-400 uppercase text-sm mb-3">{{ __('Méthodes de paiement autorisées') }}</h5>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
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

            <hr class="my-4 border-gray-200 dark:border-gray-700">

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg mr-1"></i>{{ __('global.save') }}
            </button>
        </form>
    </div>
</div>

@endsection
