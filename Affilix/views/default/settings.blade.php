@extends('layouts.client')

@section('title', __('Affilix::affiliation.settings'))

@section('content')
<div class="max-w-4xl mx-auto py-6">

{{-- Flash --}}
@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-4 text-sm text-green-700 dark:text-green-300">
        {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 mb-4 text-sm text-red-700 dark:text-red-300">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Navigation --}}
<div class="card mb-4">
    <div class="card-heading">
        <div>
            <h4>{{ __('Affilix::affiliation.settings') }}</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Code :') }} <strong class="font-mono">{{ $affiliate->referral_code }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('affiliation.dashboard') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-speedometer2 mr-1"></i>{{ __('Dashboard') }}
            </a>
            <a href="{{ route('affiliation.commissions') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-cash-stack mr-1"></i>{{ __('Commissions') }}
            </a>
            <a href="{{ route('affiliation.referrals') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-people mr-1"></i>{{ __('Parrainages') }}
            </a>
        </div>
    </div>
</div>

{{-- Formulaire --}}
<div class="card">
    <div class="card-heading">
        <h4>{{ __('Affilix::affiliation.payment_method') }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('affiliation.settings.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            @php
                $methods = [];
                if (setting('affiliation_payment_balance', '1') == '1')       $methods['balance']       = __('Balance (Fonds du compte client)');
                if (setting('affiliation_payment_paypal', '1') == '1')        $methods['paypal']        = 'PayPal';
                if (setting('affiliation_payment_bank_transfer', '1') == '1') $methods['bank_transfer'] = __('Virement bancaire');
            @endphp

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Affilix::affiliation.payment_method') }} <span class="text-red-500">*</span>
                </label>
                <select name="payment_method" id="payment_method" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach($methods as $value => $label)
                        <option value="{{ $value }}" {{ old('payment_method', $affiliate->payment_method) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Balance info --}}
            <div id="balance-details" class="{{ old('payment_method', $affiliate->payment_method) === 'balance' ? '' : 'hidden' }}">
                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-3 text-sm text-blue-700 dark:text-blue-300">
                    <i class="bi bi-info-circle mr-1"></i>
                    {{ __('Vos commissions seront ajoutées directement à votre solde de compte.') }}
                </div>
            </div>

            {{-- PayPal --}}
            <div id="paypal-details" class="{{ old('payment_method', $affiliate->payment_method) === 'paypal' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Affilix::affiliation.paypal_email') }} <span class="text-red-500">*</span>
                </label>
                <input type="email" name="payment_details[paypal_email]"
                    value="{{ old('payment_details.paypal_email', $affiliate->payment_details['paypal_email'] ?? '') }}"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="votre@email.com">
            </div>

            {{-- Virement bancaire --}}
            <div id="bank-details" class="{{ old('payment_method', $affiliate->payment_method) === 'bank_transfer' ? '' : 'hidden' }} space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">IBAN <span class="text-red-500">*</span></label>
                    <input type="text" name="payment_details[iban]"
                        value="{{ old('payment_details.iban', $affiliate->payment_details['iban'] ?? '') }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">BIC / SWIFT</label>
                    <input type="text" name="payment_details[bic]"
                        value="{{ old('payment_details.bic', $affiliate->payment_details['bic'] ?? '') }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="BNPAFRPPXXX">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg mr-1"></i>{{ __('Affilix::affiliation.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Infos compte --}}
<div class="card mt-4">
    <div class="card-heading">
        <h4>{{ __('Informations du compte') }}</h4>
    </div>
    <div class="card-body space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.referral_code') }}</span>
            <code class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded">{{ $affiliate->referral_code }}</code>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.stats.commission_rate') }}</span>
            <span class="font-medium">{{ number_format($affiliate->commission_rate, 0) }}%</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.status') }}</span>
            @if($affiliate->status === 'active')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">{{ __('Affilix::affiliation.active') }}</span>
            @elseif($affiliate->status === 'inactive')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">{{ __('En attente d\'approbation') }}</span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">{{ __('Affilix::affiliation.suspended') }}</span>
            @endif
        </div>
    </div>
</div>

</div>

<script>
document.getElementById('payment_method').addEventListener('change', function () {
    ['balance-details', 'paypal-details', 'bank-details'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });
    const map = { balance: 'balance-details', paypal: 'paypal-details', bank_transfer: 'bank-details' };
    if (map[this.value]) document.getElementById(map[this.value]).classList.remove('hidden');
});
</script>
@endsection
