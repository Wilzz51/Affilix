@extends('layouts.client')

@section('title', __('Affilix::affiliation.become_affiliate'))

@section('content')
<div class="max-w-4xl mx-auto py-6">

{{-- Messages flash --}}
@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-4 text-sm text-green-700 dark:text-green-300">
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 mb-4 text-sm text-red-700 dark:text-red-300">
        {{ session('error') }}
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

<div class="card">
    <div class="card-heading">
        <div>
            <h4>{{ __('Affilix::affiliation.become_affiliate') }}</h4>
            <p>{{ __('Rejoignez notre programme d\'affiliation et commencez à gagner des commissions') }}</p>
        </div>
    </div>
    <div class="card-body">

        {{-- Avantages --}}
        <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-4 mb-6">
            <p class="font-semibold text-blue-700 dark:text-blue-300 mb-2">{{ __('Avantages du programme') }}</p>
            <ul class="space-y-1 text-sm text-blue-600 dark:text-blue-400">
                <li><i class="bi bi-check-circle-fill mr-1"></i>{{ __('Commission de :rate% sur toutes les ventes', ['rate' => setting('default_commission_rate', 10)]) }}</li>
                <li><i class="bi bi-check-circle-fill mr-1"></i>{{ __('Tableau de bord détaillé avec statistiques en temps réel') }}</li>
                <li><i class="bi bi-check-circle-fill mr-1"></i>{{ __('Paiements via les méthodes disponibles') }}</li>
                <li><i class="bi bi-check-circle-fill mr-1"></i>{{ __('Support dédié pour les affiliés') }}</li>
            </ul>
        </div>

        <form action="{{ route('affiliation.store') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Méthode de paiement --}}
            @php
                $methods = [];
                if (setting('affiliation_payment_balance', '1') == '1') $methods['balance'] = __('Balance (Fonds du compte client)');
                if (setting('affiliation_payment_paypal', '1') == '1')   $methods['paypal'] = 'PayPal';
                if (setting('affiliation_payment_bank_transfer', '1') == '1') $methods['bank_transfer'] = __('Virement bancaire');
            @endphp

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Affilix::affiliation.payment_method') }} <span class="text-red-500">*</span>
                </label>
                <select name="payment_method" id="payment_method" required
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">-- {{ __('Sélectionnez une méthode') }} --</option>
                    @foreach($methods as $value => $label)
                        <option value="{{ $value }}" {{ old('payment_method') === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @if(empty($methods))
                    <p class="text-xs text-red-500 mt-1">{{ __('Aucune méthode de paiement disponible. Contactez l\'administrateur.') }}</p>
                @endif
            </div>

            {{-- Balance info --}}
            <div id="balance-details" class="{{ old('payment_method') === 'balance' ? '' : 'hidden' }}">
                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 p-3 text-sm text-blue-700 dark:text-blue-300">
                    <i class="bi bi-info-circle mr-1"></i>
                    {{ __('Vos commissions seront ajoutées directement à votre solde de compte.') }}
                </div>
            </div>

            {{-- PayPal --}}
            <div id="paypal-details" class="{{ old('payment_method') === 'paypal' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('Affilix::affiliation.paypal_email') }} <span class="text-red-500">*</span>
                </label>
                <input type="email" name="payment_details[paypal_email]"
                    value="{{ old('payment_details.paypal_email') }}"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                    placeholder="votre@email.com">
            </div>

            {{-- Virement bancaire --}}
            <div id="bank-details" class="{{ old('payment_method') === 'bank_transfer' ? '' : 'hidden' }} space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        IBAN <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="payment_details[iban]"
                        value="{{ old('payment_details.iban') }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="FR76 XXXX XXXX XXXX XXXX XXXX XXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">BIC / SWIFT</label>
                    <input type="text" name="payment_details[bic]"
                        value="{{ old('payment_details.bic') }}"
                        class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                        placeholder="BNPAFRPPXXX">
                </div>
            </div>

            {{-- CGU --}}
            <div class="flex items-start gap-2">
                <input type="checkbox" name="terms" id="terms" required value="1"
                    class="mt-1 rounded border-gray-300 dark:border-gray-600">
                <label for="terms" class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('J\'accepte les conditions du programme d\'affiliation') }}
                </label>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="/client" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    ← {{ __('Retour') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-person-check mr-1"></i>
                    {{ __('Affilix::affiliation.register') }}
                </button>
            </div>
        </form>
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

</div>{{-- Fin max-w-4xl --}}
@endsection
