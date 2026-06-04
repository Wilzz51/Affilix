@extends('admin.layouts.admin')

@section('title', __('Créer un affilié'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap items-center gap-4 mb-6">
    <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary btn-sm shrink-0">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Créer un affilié') }}</h1>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">{{ __('Inscrire manuellement un client au programme d\'affiliation') }}</p>
    </div>
</div>

@if($errors->any())
    <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 mb-5 text-sm text-red-700 dark:text-red-300 flex gap-2.5">
        <i class="bi bi-exclamation-circle-fill mt-0.5 shrink-0"></i>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="max-w-2xl">
<form action="{{ route('affiliation.admin.store') }}" method="POST">
    @csrf

    <div class="card">
        <div class="card-heading">
            <div class="flex items-center gap-2">
                <i class="bi bi-person-plus text-gray-400 dark:text-gray-500"></i>
                <h4>{{ __('Informations') }}</h4>
            </div>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">

            {{-- Client --}}
            <div class="flex items-start justify-between gap-6 px-5 py-5">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Client') }} <span class="text-red-500">*</span></p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Seuls les clients sans compte affilié sont listés.') }}</p>
                </div>
                <div class="shrink-0 w-64">
                    <select name="customer_id" required
                        class="w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 text-sm text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
                        <option value="">{{ __('Choisir un client…') }}</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->firstname ?? '' }} {{ $customer->lastname ?? ($customer->name ?? '') }} — {{ $customer->email }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Type + taux de commission --}}
            <div class="px-5 py-5">
                <div class="flex items-start justify-between gap-6 mb-4">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Commission') }} <span class="text-red-500">*</span></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Pourcentage sur la vente ou montant fixe par commande.') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Toggle % / € --}}
                    <div class="flex gap-1 bg-gray-100 dark:bg-gray-700 rounded-lg p-1" id="commission-type-btns">
                        <button type="button" onclick="setCommissionType('percent')" id="btn-percent"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm">
                            %
                        </button>
                        <button type="button" onclick="setCommissionType('fixed')" id="btn-fixed"
                            class="px-3 py-1.5 rounded-md text-xs font-medium transition-all text-gray-500 dark:text-gray-400">
                            {{ setting('currency_symbol', '€') }}
                        </button>
                    </div>
                    <input type="hidden" name="commission_type" id="commission_type" value="{{ old('commission_type', 'percent') }}">
                    <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 flex-1">
                        <input type="number" name="commission_rate" id="commission_rate"
                            value="{{ old('commission_rate', setting('default_commission_rate', 10)) }}"
                            min="0" max="99999" step="0.01"
                            class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-full text-right"
                            required>
                        <span id="commission_unit" class="text-sm text-gray-400 dark:text-gray-500 shrink-0">%</span>
                    </div>
                </div>
            </div>

            {{-- Statut --}}
            <div class="px-5 py-5">
                <p class="text-sm font-medium text-gray-900 dark:text-white mb-3">{{ __('Statut') }} <span class="text-red-500">*</span></p>
                @php
                    $currentStatus = old('status', 'active');
                    $cardBase = 'status-card flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 transition-all cursor-pointer';
                    $cardOff  = 'border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500';
                    $cardOn   = ['active' => 'border-green-500 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400', 'inactive' => 'border-yellow-500 bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400', 'suspended' => 'border-red-500 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400'];
                    $cardIcon = ['active' => 'bi-check-circle-fill', 'inactive' => 'bi-pause-circle-fill', 'suspended' => 'bi-x-circle-fill'];
                    $cardLabel = ['active' => __('Affilix::affiliation.active'), 'inactive' => __('Affilix::affiliation.inactive'), 'suspended' => __('Affilix::affiliation.suspended')];
                @endphp
                <div class="grid grid-cols-3 gap-3">
                    @foreach(['active', 'inactive', 'suspended'] as $st)
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="{{ $st }}" class="sr-only status-radio" {{ $currentStatus === $st ? 'checked' : '' }}>
                        <div data-status="{{ $st }}" data-on-classes="{{ $cardOn[$st] }}" data-off-classes="{{ $cardOff }}"
                            class="{{ $cardBase }} {{ $currentStatus === $st ? $cardOn[$st] : $cardOff }}">
                            <i class="bi {{ $cardIcon[$st] }} text-2xl"></i>
                            <span class="text-xs font-semibold">{{ $cardLabel[$st] }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Méthode de paiement --}}
            <div class="flex items-center justify-between gap-6 px-5 py-5">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Méthode de paiement') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ __('L\'affilié pourra la modifier depuis son espace.') }}</p>
                </div>
                <select name="payment_method"
                    class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 text-sm text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary shrink-0">
                    <option value="balance" {{ old('payment_method', 'balance') === 'balance' ? 'selected' : '' }}>Balance</option>
                    <option value="paypal" {{ old('payment_method') === 'paypal' ? 'selected' : '' }}>PayPal</option>
                    <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>{{ __('Virement bancaire') }}</option>
                </select>
            </div>

        </div>

        <div class="flex justify-end gap-3 px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-person-plus mr-1"></i>{{ __('Créer l\'affilié') }}
            </button>
        </div>
    </div>

</form>
</div>

</div>

<script>
function setCommissionType(type) {
    document.getElementById('commission_type').value = type;
    document.getElementById('commission_unit').textContent = type === 'percent' ? '%' : '{{ setting('currency_symbol', '€') }}';

    var btnPercent = document.getElementById('btn-percent');
    var btnFixed   = document.getElementById('btn-fixed');
    var activeClass   = 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm';
    var inactiveClass = 'text-gray-500 dark:text-gray-400';

    if (type === 'percent') {
        btnPercent.className = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all ' + activeClass;
        btnFixed.className   = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all ' + inactiveClass;
    } else {
        btnFixed.className   = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all ' + activeClass;
        btnPercent.className = 'px-3 py-1.5 rounded-md text-xs font-medium transition-all ' + inactiveClass;
    }
}

// Init depuis old() si erreur de validation
setCommissionType('{{ old('commission_type', 'percent') }}');

document.querySelectorAll('.status-radio').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('.status-card').forEach(card => {
            const on  = card.dataset.onClasses.split(' ').filter(Boolean);
            const off = card.dataset.offClasses.split(' ').filter(Boolean);
            card.classList.remove(...on, ...off);
            card.classList.add(...(card.dataset.status === radio.value ? on : off));
        });
    });
});
</script>
@endsection
