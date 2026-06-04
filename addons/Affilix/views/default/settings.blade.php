@extends('layouts.client')

@section('title', __('Affilix::affiliation.settings'))

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-5">

{{-- Flash --}}
@if(session('success'))
    <div class="rounded-2xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 text-sm text-green-700 dark:text-green-300 flex items-center gap-2.5">
        <i class="bi bi-check-circle-fill shrink-0"></i>{{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div class="rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 text-sm text-red-700 dark:text-red-300 flex gap-2.5">
        <i class="bi bi-exclamation-circle-fill mt-0.5 shrink-0"></i>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('affiliation.dashboard') }}"
            class="h-9 w-9 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors shrink-0">
            <i class="bi bi-arrow-left text-gray-600 dark:text-gray-400 text-sm"></i>
        </a>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Paramètres') }}</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.commissions') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-cash-stack mr-1"></i>{{ __('Commissions') }}
        </a>
        <a href="{{ route('affiliation.referrals') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-people mr-1"></i>{{ __('Parrainages') }}
        </a>
    </div>
</div>

{{-- Infos compte --}}
<div class="card shadow-sm">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <div class="h-7 w-7 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i class="bi bi-person text-primary text-xs"></i>
            </div>
            <h4 class="text-sm">{{ __('Mon compte affilié') }}</h4>
        </div>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
        <div class="flex justify-between items-center px-5 py-3">
            <span class="text-gray-500 dark:text-gray-400">{{ __('Code de parrainage') }}</span>
            <code class="font-mono text-xs bg-gray-100 dark:bg-gray-700 px-2.5 py-1 rounded-lg text-gray-700 dark:text-gray-300">{{ $affiliate->referral_code }}</code>
        </div>
        <div class="flex justify-between items-center px-5 py-3">
            <span class="text-gray-500 dark:text-gray-400">{{ __('Commission') }}</span>
            <span class="font-semibold text-gray-900 dark:text-white">
                {{ number_format($affiliate->commission_rate, ($affiliate->commission_type ?? 'percent') === 'fixed' ? 2 : 0) }}{{ ($affiliate->commission_type ?? 'percent') === 'fixed' ? ' ' . setting('currency_symbol', '€') : '%' }}
            </span>
        </div>
        <div class="flex justify-between items-center px-5 py-3">
            <span class="text-gray-500 dark:text-gray-400">{{ __('Statut') }}</span>
            @if($affiliate->status === 'active')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                    <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Actif') }}
                </span>
            @elseif($affiliate->status === 'inactive')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                    <i class="bi bi-circle-fill text-[6px]"></i>{{ __('En attente') }}
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                    <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Suspendu') }}
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Méthode de paiement --}}
<div class="card shadow-sm">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <div class="h-7 w-7 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-wallet2 text-green-600 dark:text-green-400 text-xs"></i>
            </div>
            <h4 class="text-sm">{{ __('Méthode de paiement') }}</h4>
        </div>
    </div>
    <div class="p-5">
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
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('Méthode') }} <span class="text-red-500">*</span>
                </label>
                <select name="payment_method" id="payment_method" required
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition">
                    @foreach($methods as $value => $label)
                        <option value="{{ $value }}" {{ old('payment_method', $affiliate->payment_method) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Balance info --}}
            <div id="balance-details" class="{{ old('payment_method', $affiliate->payment_method) === 'balance' ? '' : 'hidden' }}">
                <div class="rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-3.5 text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
                    <i class="bi bi-info-circle shrink-0"></i>
                    {{ __('Vos commissions seront ajoutées directement à votre solde de compte.') }}
                </div>
            </div>

            {{-- PayPal --}}
            <div id="paypal-details" class="{{ old('payment_method', $affiliate->payment_method) === 'paypal' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('Adresse PayPal') }} <span class="text-red-500">*</span>
                </label>
                <input type="email" name="payment_details[paypal_email]"
                    value="{{ old('payment_details.paypal_email', $affiliate->payment_details['paypal_email'] ?? '') }}"
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                    placeholder="votre@email.com">
            </div>

            {{-- Virement bancaire --}}
            <div id="bank-details" class="{{ old('payment_method', $affiliate->payment_method) === 'bank_transfer' ? '' : 'hidden' }} space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">IBAN <span class="text-red-500">*</span></label>
                    <input type="text" name="payment_details[iban]"
                        value="{{ old('payment_details.iban', $affiliate->payment_details['iban'] ?? '') }}"
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">BIC / SWIFT</label>
                    <input type="text" name="payment_details[bic]"
                        value="{{ old('payment_details.bic', $affiliate->payment_details['bic'] ?? '') }}"
                        class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                        placeholder="BNPAFRPPXXX">
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg mr-1.5"></i>{{ __('Enregistrer') }}
                </button>
            </div>
        </form>
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
