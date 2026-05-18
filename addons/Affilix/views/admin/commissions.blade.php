@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.commissions'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap justify-between items-start gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="bi bi-cash-stack text-primary"></i>{{ __('Affilix::affiliation.commissions') }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Gérez et traitez les commissions de vos affiliés') }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.admin.commissions.export', request()->only(['status', 'affiliate_id', 'date_from', 'date_to'])) }}"
            class="btn btn-secondary btn-sm" title="{{ __('Exporter en CSV') }}">
            <i class="bi bi-download mr-1"></i>{{ __('Export CSV') }}
        </a>
        <a href="{{ route('affiliation.admin.withdrawals') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-wallet2 mr-1"></i>{{ __('Retraits') }}
            @php $pendingCount = \App\Addons\Affiliation\Models\AffiliateWithdrawal::where('status','pending')->count(); @endphp
            @if($pendingCount > 0)
                <span class="ml-1 inline-flex items-center justify-center h-4 min-w-[1rem] px-1 rounded-full text-[10px] font-bold bg-red-500 text-white">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-people mr-1"></i>{{ __('Affilix::affiliation.admin.affiliates') }}
        </a>
        <a href="{{ route('affiliation.admin.settings') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-gear mr-1"></i>{{ __('Affilix::affiliation.settings') }}
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
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card" style="border-left: 4px solid #eab308;">
        <div class="card-body flex items-center gap-4">
            <div class="h-11 w-11 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400 truncate">{{ number_format($stats['pending'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Affilix::affiliation.pending') }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #3b82f6;">
        <div class="card-body flex items-center gap-4">
            <div class="h-11 w-11 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check2-circle text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 truncate">{{ number_format($stats['approved'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Affilix::affiliation.approved') }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #22c55e;">
        <div class="card-body flex items-center gap-4">
            <div class="h-11 w-11 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400 truncate">{{ number_format($stats['paid'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Affilix::affiliation.paid') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Table + bulk actions --}}
<div class="card">
    <div class="card-heading flex-wrap gap-y-3">
        {{-- Status filter tabs --}}
        @php $currentStatus = request('status', ''); @endphp
        <div class="flex items-center gap-1 flex-wrap">
            @foreach(['' => __('Tous'), 'pending' => __('Affilix::affiliation.pending'), 'approved' => __('Affilix::affiliation.approved'), 'paid' => __('Affilix::affiliation.paid'), 'cancelled' => __('Annulée')] as $value => $label)
                <a href="{{ request()->fullUrlWithQuery(['status' => $value, 'page' => 1]) }}"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors
                        {{ $currentStatus === $value
                            ? 'bg-primary text-white shadow-sm'
                            : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Bulk actions --}}
        <div class="flex items-center gap-2 ml-auto">
            <span id="selection-badge" class="hidden text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2.5 py-1.5 rounded-lg whitespace-nowrap">
                <span id="selection-count">0</span> {{ __('sélectionnée(s)') }}
            </span>
            <button type="button" id="btn-approve"
                onclick="submitAction('{{ route('affiliation.admin.commissions.approve') }}')"
                class="btn btn-sm btn-primary opacity-40 cursor-not-allowed" disabled>
                <i class="bi bi-check-lg mr-1"></i>{{ __('Affilix::affiliation.admin.approve_selected') }}
            </button>
            <button type="button" id="btn-pay"
                onclick="submitPay()"
                class="btn btn-sm btn-secondary opacity-40 cursor-not-allowed" disabled>
                <i class="bi bi-cash mr-1"></i>{{ __('Affilix::affiliation.admin.pay_selected') }}
            </button>
        </div>
    </div>

    {{-- Filtres avancés --}}
    <form method="GET" action="{{ route('affiliation.admin.commissions') }}"
          class="flex items-center gap-2 px-4 py-2.5 border-b border-gray-100 dark:border-gray-700 overflow-x-auto">
            <input type="hidden" name="status" value="{{ request('status') }}">

            {{-- Affilié --}}
            <div class="flex items-center bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 gap-2 shrink-0">
                <i class="bi bi-person text-gray-400 text-sm shrink-0"></i>
                <select name="affiliate_id"
                    class="bg-transparent text-sm text-gray-700 dark:text-gray-200 border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-[150px] cursor-pointer">
                    <option value="">{{ __('Tous les affiliés') }}</option>
                    @foreach($affiliates as $aff)
                        <option value="{{ $aff->id }}" {{ request('affiliate_id') == $aff->id ? 'selected' : '' }}>
                            {{ trim(($aff->customer->firstname ?? '') . ' ' . ($aff->customer->lastname ?? ($aff->customer->name ?? ''))) ?: '#' . $aff->id }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Plage de dates --}}
            <div class="flex items-center bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 gap-2 shrink-0">
                <i class="bi bi-calendar3 text-gray-400 text-xs shrink-0"></i>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="bg-transparent text-sm text-gray-700 dark:text-gray-200 border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-[115px] cursor-pointer">
                <span class="text-gray-300 dark:text-gray-600 text-xs shrink-0">–</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="bg-transparent text-sm text-gray-700 dark:text-gray-200 border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-[115px] cursor-pointer">
            </div>

            {{-- Raccourcis période --}}
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/60 rounded-lg p-1 shrink-0">
                <button type="button" onclick="setThisMonth()"
                    class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 dark:text-gray-400
                           hover:bg-white dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-white hover:shadow-sm">
                    {{ __('Ce mois') }}
                </button>
                <button type="button" onclick="setLastMonth()"
                    class="px-3 py-1 text-xs font-medium rounded-md transition-colors text-gray-500 dark:text-gray-400
                           hover:bg-white dark:hover:bg-gray-600 hover:text-gray-800 dark:hover:text-white hover:shadow-sm">
                    {{ __('Mois préc.') }}
                </button>
            </div>

            {{-- Actions --}}
            <button type="submit" class="btn btn-sm btn-primary shrink-0">
                <i class="bi bi-funnel-fill mr-1 text-[11px]"></i>{{ __('Filtrer') }}
            </button>
            @if(request('affiliate_id') || request('date_from') || request('date_to'))
            <a href="{{ route('affiliation.admin.commissions', array_filter(['status' => request('status')])) }}"
                class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors flex items-center gap-0.5 shrink-0">
                <i class="bi bi-x-circle text-sm"></i>{{ __('Réinitialiser') }}
            </a>
            @endif
    </form>

    <form id="commissions-form" method="POST">
    @csrf
    <div class="overflow-x-auto">
        <table class="table w-full table-fixed">
            <thead>
                <tr>
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" id="check-all" class="rounded cursor-pointer">
                    </th>
                    <th class="px-4 py-3 w-[13%] text-left">{{ __('Date') }}</th>
                    <th class="px-4 py-3 w-[18%] text-left">{{ __('Affilié') }}</th>
                    <th class="px-4 py-3 w-[18%] text-left">{{ __('Affilix::affiliation.customer') }}</th>
                    <th class="px-4 py-3 w-[10%] text-left">{{ __('Affilix::affiliation.invoice') }}</th>
                    <th class="px-4 py-3 w-[14%] text-right">{{ __('Affilix::affiliation.amount') }}</th>
                    <th class="px-4 py-3 w-[10%] text-center">{{ __('Taux') }}</th>
                    <th class="px-4 py-3 w-[17%] text-left">{{ __('Affilix::affiliation.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $commission)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3">
                        <input type="checkbox" name="commission_ids[]" value="{{ $commission->id }}"
                            class="commission-checkbox rounded cursor-pointer">
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $commission->created_at->format('d/m/Y') }}</span>
                        <span class="block text-xs text-gray-400 dark:text-gray-500">{{ $commission->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('affiliation.admin.show', $commission->affiliate) }}"
                            class="text-sm font-medium text-primary hover:underline whitespace-nowrap">
                            {{ $commission->affiliate->customer->firstname ?? '' }}
                            {{ $commission->affiliate->customer->lastname ?? ($commission->affiliate->customer->name ?? '—') }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                        @if($commission->type === 'click')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                <i class="bi bi-cursor-fill text-[9px]"></i>{{ __('Clic') }}
                            </span>
                        @else
                            {{ $commission->referral?->customer?->firstname ?? '' }}
                            {{ $commission->referral?->customer?->lastname ?? ($commission->referral?->customer?->name ?? '—') }}
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($commission->invoice_id)
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md whitespace-nowrap">#{{ $commission->invoice_id }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($commission->amount, 2) }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-0.5">{{ setting('currency_symbol', '€') }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ number_format($commission->commission_rate, 0) }}%</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($commission->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.paid') }}
                            </span>
                            @if($commission->paid_at)
                                <span class="block text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $commission->paid_at->format('d/m/Y') }}</span>
                            @endif
                            @if($commission->payment_reference)
                                <span class="block text-xs font-mono text-gray-400 dark:text-gray-500 truncate max-w-[140px]" title="{{ $commission->payment_reference }}">{{ $commission->payment_reference }}</span>
                            @endif
                        @elseif($commission->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.approved') }}
                            </span>
                            @if($commission->approved_at)
                                <span class="block text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $commission->approved_at->format('d/m/Y') }}</span>
                            @endif
                            <form method="POST" action="{{ route('affiliation.admin.commissions.cancel', $commission) }}" class="mt-1"
                                onsubmit="return confirm('{{ __('Annuler cette commission ?') }}')">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors flex items-center gap-0.5">
                                    <i class="bi bi-x-circle text-[11px]"></i>{{ __('Annuler') }}
                                </button>
                            </form>
                        @elseif($commission->status === 'cancelled')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Annulée') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.pending') }}
                            </span>
                            <form method="POST" action="{{ route('affiliation.admin.commissions.cancel', $commission) }}" class="mt-1"
                                onsubmit="return confirm('{{ __('Annuler cette commission ?') }}')">
                                @csrf
                                <button type="submit" class="text-xs text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors flex items-center gap-0.5">
                                    <i class="bi bi-x-circle text-[11px]"></i>{{ __('Annuler') }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="flex flex-col items-center justify-center gap-3 py-16 w-full">
                            <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="bi bi-cash-stack text-3xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aucune commission') }}</p>
                                @if(request('status'))
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Essayez de changer le filtre de statut.') }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($commissions->hasPages())
    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700">
        {{ $commissions->links('admin.shared.layouts.pagination') }}
    </div>
    @endif
    </form>
</div>

{{-- Payment modal --}}
<div id="pay-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex items-start gap-4 mb-5">
            <div class="h-11 w-11 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
                <i class="bi bi-cash text-primary text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Marquer comme payées') }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    <span id="pay-modal-count" class="font-semibold text-gray-700 dark:text-gray-300">0</span>
                    {{ __('commission(s) seront marquées comme payées.') }}
                </p>
            </div>
        </div>
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Affilix::affiliation.admin.payment_reference') }}
                <span class="text-gray-400 font-normal ml-1">{{ __('(optionnel)') }}</span>
            </label>
            <input type="text" id="pay-reference"
                class="input w-full"
                placeholder="{{ __('Ex : virement-2026-05') }}">
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">{{ __('Identifiant du virement ou de la transaction.') }}</p>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
            <button type="button" onclick="document.getElementById('pay-modal').classList.add('hidden')"
                class="btn btn-secondary">{{ __('Annuler') }}</button>
            <button type="button" onclick="submitAction('{{ route('affiliation.admin.commissions.pay') }}', true)"
                class="btn btn-primary">
                <i class="bi bi-cash mr-1"></i>{{ __('Affilix::affiliation.admin.pay') }}
            </button>
        </div>
    </div>
</div>

</div>

<script>
function setThisMonth() {
    const now = new Date();
    const y = now.getFullYear(), m = String(now.getMonth() + 1).padStart(2, '0');
    const last = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
    document.querySelector('[name=date_from]').value = y + '-' + m + '-01';
    document.querySelector('[name=date_to]').value   = y + '-' + m + '-' + String(last).padStart(2, '0');
}
function setLastMonth() {
    const now   = new Date();
    const first = new Date(now.getFullYear(), now.getMonth() - 1, 1);
    const last  = new Date(now.getFullYear(), now.getMonth(), 0);
    const pad = n => String(n).padStart(2, '0');
    document.querySelector('[name=date_from]').value = first.getFullYear() + '-' + pad(first.getMonth() + 1) + '-01';
    document.querySelector('[name=date_to]').value   = last.getFullYear()  + '-' + pad(last.getMonth()  + 1) + '-' + pad(last.getDate());
}

(function () {
    const checkAll    = document.getElementById('check-all');
    const badge       = document.getElementById('selection-badge');
    const countEl     = document.getElementById('selection-count');
    const payCountEl  = document.getElementById('pay-modal-count');
    const btnApprove  = document.getElementById('btn-approve');
    const btnPay      = document.getElementById('btn-pay');

    function getCheckboxes() { return document.querySelectorAll('.commission-checkbox'); }
    function getChecked()    { return document.querySelectorAll('.commission-checkbox:checked'); }

    function setButtonEnabled(btn, enabled) {
        if (enabled) {
            btn.removeAttribute('disabled');
            btn.classList.remove('opacity-40', 'cursor-not-allowed');
        } else {
            btn.setAttribute('disabled', '');
            btn.classList.add('opacity-40', 'cursor-not-allowed');
        }
    }

    function updateUI() {
        const n   = getChecked().length;
        const all = getCheckboxes().length;

        countEl.textContent    = n;
        payCountEl.textContent = n;

        badge.classList.toggle('hidden', n === 0);
        setButtonEnabled(btnApprove, n > 0);
        setButtonEnabled(btnPay, n > 0);

        checkAll.indeterminate = n > 0 && n < all;
        checkAll.checked       = all > 0 && n === all;
    }

    checkAll.addEventListener('change', function () {
        getCheckboxes().forEach(cb => cb.checked = this.checked);
        updateUI();
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('commission-checkbox')) updateUI();
    });

    window.submitAction = function (url, withRef) {
        if (getChecked().length === 0) return;
        const form = document.getElementById('commissions-form');
        form.action = url;
        if (withRef) {
            const ref   = document.getElementById('pay-reference').value;
            const input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = 'payment_reference';
            input.value = ref;
            form.appendChild(input);
            document.getElementById('pay-modal').classList.add('hidden');
        }
        form.submit();
    };

    window.submitPay = function () {
        if (getChecked().length === 0) return;
        payCountEl.textContent = getChecked().length;
        document.getElementById('pay-modal').classList.remove('hidden');
    };

    document.getElementById('pay-modal').addEventListener('click', function (e) {
        if (e.target === this) this.classList.add('hidden');
    });
})();
</script>
@endsection
