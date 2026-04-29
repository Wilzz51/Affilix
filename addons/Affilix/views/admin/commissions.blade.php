@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.commissions'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        <i class="bi bi-cash-stack mr-2 text-primary"></i>{{ __('Affilix::affiliation.commissions') }}
    </h1>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-people mr-1"></i>{{ __('Affilix::affiliation.admin.affiliates') }}
        </a>
        <a href="{{ route('affiliation.admin.settings') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-gear mr-1"></i>{{ __('Affilix::affiliation.settings') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-4 text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card">
        <div class="card-body flex items-center gap-4">
            <div class="h-10 w-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format($stats['pending'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Affilix::affiliation.pending') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-4">
            <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check2 text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['approved'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Affilix::affiliation.approved') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-4">
            <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['paid'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Affilix::affiliation.paid') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Tableau avec actions groupées --}}
<form id="commissions-form" method="POST">
@csrf

<div class="card">
    <div class="card-heading">
        <h4>{{ __('Affilix::affiliation.commissions') }}</h4>
        <div class="flex gap-2 ml-auto">
            <button type="button" onclick="submitAction('{{ route('affiliation.admin.commissions.approve') }}')"
                class="btn btn-sm btn-primary">
                <i class="bi bi-check-lg mr-1"></i>{{ __('Affilix::affiliation.admin.approve_selected') }}
            </button>
            <button type="button" onclick="submitPay()"
                class="btn btn-sm btn-secondary">
                <i class="bi bi-cash mr-1"></i>{{ __('Affilix::affiliation.admin.pay_selected') }}
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-4 py-3 w-8"><input type="checkbox" id="check-all" class="rounded"></th>
                    <th class="px-4 py-3">{{ __('Date') }}</th>
                    <th class="px-4 py-3">{{ __('Affilié') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.customer') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.invoice') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('Affilix::affiliation.amount') }}</th>
                    <th class="px-4 py-3 text-center">{{ __('Taux') }}</th>
                    <th class="px-4 py-3">{{ __('Affilix::affiliation.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $commission)
                <tr>
                    <td class="px-4 py-3">
                        <input type="checkbox" name="commission_ids[]" value="{{ $commission->id }}"
                            class="commission-checkbox rounded">
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $commission->created_at->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('affiliation.admin.show', $commission->affiliate) }}"
                            class="text-sm font-medium text-primary hover:underline whitespace-nowrap">
                            {{ $commission->affiliate->customer->firstname ?? '' }}
                            {{ $commission->affiliate->customer->lastname ?? ($commission->affiliate->customer->name ?? '—') }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        {{ $commission->referral->customer->firstname ?? '' }}
                        {{ $commission->referral->customer->lastname ?? ($commission->referral->customer->name ?? '—') }}
                    </td>
                    <td class="px-4 py-3">
                        @if($commission->invoice)
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded">#{{ $commission->invoice_id }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm font-semibold text-right whitespace-nowrap">{{ number_format($commission->amount, 2) }} {{ setting('currency_symbol', '€') }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">{{ number_format($commission->commission_rate, 0) }}%</td>
                    <td class="px-4 py-3">
                        @if($commission->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.paid') }}
                            </span>
                        @elseif($commission->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.approved') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.pending') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center">
                        <i class="bi bi-cash-stack text-4xl text-gray-300 dark:text-gray-600 block mb-2"></i>
                        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucune commission') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-3">
        {{ $commissions->links('admin.shared.layouts.pagination') }}
    </div>
</div>
</form>

{{-- Modal paiement --}}
<div id="pay-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                <i class="bi bi-cash text-primary text-lg"></i>
            </div>
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Affilix::affiliation.admin.payment_reference') }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Optionnel — identifiant du virement ou de la transaction.') }}</p>
            </div>
        </div>
        <input type="text" id="pay-reference"
            class="input w-full mb-4"
            placeholder="{{ __('Ex : virement-2026-05') }}">
        <div class="flex justify-end gap-2">
            <button onclick="document.getElementById('pay-modal').classList.add('hidden')"
                class="btn btn-secondary">{{ __('Annuler') }}</button>
            <button onclick="submitAction('{{ route('affiliation.admin.commissions.pay') }}', true)"
                class="btn btn-primary">
                <i class="bi bi-cash mr-1"></i>{{ __('Affilix::affiliation.admin.pay') }}
            </button>
        </div>
    </div>
</div>

</div>

<script>
document.getElementById('check-all').addEventListener('change', function () {
    document.querySelectorAll('.commission-checkbox').forEach(cb => cb.checked = this.checked);
});

function submitAction(url, withRef = false) {
    const form = document.getElementById('commissions-form');
    form.action = url;
    if (withRef) {
        const ref = document.getElementById('pay-reference').value;
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'payment_reference';
        input.value = ref;
        form.appendChild(input);
        document.getElementById('pay-modal').classList.add('hidden');
    }
    form.submit();
}

function submitPay() {
    const checked = document.querySelectorAll('.commission-checkbox:checked');
    if (checked.length === 0) {
        alert('{{ __("Sélectionnez au moins une commission.") }}');
        return;
    }
    document.getElementById('pay-modal').classList.remove('hidden');
}
</script>
@endsection
