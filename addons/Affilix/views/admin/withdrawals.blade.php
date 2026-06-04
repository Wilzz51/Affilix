@extends('admin.layouts.admin')

@section('title', __('Demandes de paiement'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap items-center gap-4 mb-6">
    <a href="{{ route('affiliation.admin.commissions') }}" class="btn btn-secondary btn-sm shrink-0">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('Demandes de paiement') }}</h1>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">{{ __('Retraits demandés par les affiliés') }}</p>
    </div>
</div>

@if(session('success'))
    <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-5 text-sm text-green-700 dark:text-green-300 flex gap-2.5">
        <i class="bi bi-check-circle-fill mt-0.5 shrink-0"></i>{{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 mb-5 text-sm text-red-700 dark:text-red-300 flex gap-2.5">
        <i class="bi bi-exclamation-circle-fill mt-0.5 shrink-0"></i>{{ session('error') }}
    </div>
@endif

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-yellow-600 dark:text-yellow-400">{{ $stats['pending_count'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('En attente') }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ number_format($stats['pending_amount'], 2) }} {{ setting('currency_symbol', '€') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle-fill text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold tracking-tight text-green-600 dark:text-green-400">{{ number_format($stats['paid_total'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">{{ __('Total payé') }}</p>
            </div>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-link-45deg text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div class="flex flex-col gap-1.5">
                <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ __('Accès rapides') }}</p>
                <a href="{{ route('affiliation.admin.commissions') }}" class="text-xs text-primary hover:underline flex items-center gap-1">
                    <i class="bi bi-cash-stack text-[11px]"></i>{{ __('Commissions') }}
                </a>
                <a href="{{ route('affiliation.admin.index') }}" class="text-xs text-primary hover:underline flex items-center gap-1">
                    <i class="bi bi-people text-[11px]"></i>{{ __('Affiliés') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <i class="bi bi-wallet2 text-gray-400 dark:text-gray-500 text-sm"></i>
            <h4 class="text-sm">{{ __('Demandes') }}</h4>
        </div>
        {{-- Filtre statut --}}
        <form method="GET" action="{{ route('affiliation.admin.withdrawals') }}" class="flex items-center gap-2">
            <div class="flex items-center bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 gap-2 shrink-0">
                <i class="bi bi-funnel text-xs text-gray-400 dark:text-gray-500"></i>
                <select name="status" onchange="this.form.submit()" class="text-sm text-gray-700 dark:text-gray-300 bg-transparent border-0 outline-none focus:ring-0 cursor-pointer pr-1">
                    <option value="">{{ __('Tous les statuts') }}</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>{{ __('En attente') }}</option>
                    <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>{{ __('Payé') }}</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>{{ __('Rejeté') }}</option>
                </select>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="table w-full table-fixed">
            <colgroup>
                <col class="w-10">
                <col class="w-44">
                <col class="w-28">
                <col class="w-28">
                <col class="w-32">
                <col class="w-32">
                <col class="w-28">
                <col class="w-36">
            </colgroup>
            <thead>
                <tr>
                    <th class="px-4 py-3 text-xs">#</th>
                    <th class="px-4 py-3 text-xs">{{ __('Affilié') }}</th>
                    <th class="px-4 py-3 text-xs text-right">{{ __('Montant') }}</th>
                    <th class="px-4 py-3 text-xs">{{ __('Méthode') }}</th>
                    <th class="px-4 py-3 text-xs">{{ __('Référence') }}</th>
                    <th class="px-4 py-3 text-xs">{{ __('Date demande') }}</th>
                    <th class="px-4 py-3 text-xs">{{ __('Statut') }}</th>
                    <th class="px-4 py-3 text-xs text-right">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $withdrawal)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500">#{{ $withdrawal->id }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('affiliation.admin.show', $withdrawal->affiliate) }}" class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-primary truncate block">
                            {{ $withdrawal->affiliate->customer->firstname ?? '' }} {{ $withdrawal->affiliate->customer->lastname ?? ($withdrawal->affiliate->customer->name ?? '—') }}
                        </a>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $withdrawal->affiliate->customer->email ?? '' }}</span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($withdrawal->amount, 2) }} {{ setting('currency_symbol', '€') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($withdrawal->payment_method === 'balance')
                            <span class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400"><i class="bi bi-wallet2"></i>{{ __('Solde') }}</span>
                        @elseif($withdrawal->payment_method === 'paypal')
                            <span class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400"><i class="bi bi-paypal"></i>PayPal</span>
                            @if($withdrawal->payment_details['paypal_email'] ?? null)
                                <span class="block text-[11px] text-gray-400 dark:text-gray-500 truncate">{{ $withdrawal->payment_details['paypal_email'] }}</span>
                            @endif
                        @elseif($withdrawal->payment_method === 'bank_transfer')
                            <span class="inline-flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400"><i class="bi bi-bank"></i>{{ __('Virement') }}</span>
                            @if($withdrawal->payment_details['iban'] ?? null)
                                <span class="block text-[11px] text-gray-400 dark:text-gray-500 font-mono truncate">{{ substr($withdrawal->payment_details['iban'], 0, 8) }}…</span>
                            @endif
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if($withdrawal->payment_reference)
                            <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded font-mono">{{ $withdrawal->payment_reference }}</code>
                        @elseif($withdrawal->admin_note)
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic truncate block">{{ $withdrawal->admin_note }}</span>
                        @else
                            <span class="text-xs text-gray-300 dark:text-gray-600">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $withdrawal->created_at->format('d/m/Y') }}
                        <span class="block text-xs text-gray-400 dark:text-gray-500">{{ $withdrawal->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($withdrawal->status === 'pending')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-hourglass-split text-[10px]"></i>{{ __('En attente') }}
                            </span>
                        @elseif($withdrawal->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-check-circle text-[10px]"></i>{{ __('Payé') }}
                            </span>
                            @if($withdrawal->paid_at)
                                <span class="block text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">{{ $withdrawal->paid_at->format('d/m/Y') }}</span>
                            @endif
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">
                                <i class="bi bi-x-circle text-[10px]"></i>{{ __('Rejeté') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right whitespace-nowrap">
                        @if($withdrawal->status === 'pending')
                            <button type="button"
                                onclick="openPayModal({{ $withdrawal->id }}, '{{ number_format($withdrawal->amount, 2) }}')"
                                class="btn btn-sm btn-primary text-xs">
                                <i class="bi bi-check-lg mr-1"></i>{{ __('Payer') }}
                            </button>
                            <button type="button"
                                onclick="openRejectModal({{ $withdrawal->id }})"
                                class="btn btn-sm btn-secondary text-xs ml-1">
                                <i class="bi bi-x-lg mr-1"></i>{{ __('Rejeter') }}
                            </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-sm text-gray-400 dark:text-gray-500 py-10">
                        <i class="bi bi-wallet2 text-3xl block mb-2 opacity-30"></i>
                        {{ __('Aucune demande de paiement') }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($withdrawals->hasPages())
        <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
            {{ $withdrawals->links() }}
        </div>
    @endif
</div>

</div>

{{-- Modal Payer --}}
<div id="pay-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md mx-4 p-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">{{ __('Confirmer le paiement') }}</h3>
        <p id="pay-modal-text" class="text-sm text-gray-500 dark:text-gray-400 mb-4"></p>
        <form id="pay-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="text-xs font-medium text-gray-700 dark:text-gray-300 block mb-1.5">{{ __('Référence de paiement') }} <span class="text-gray-400">({{ __('optionnel') }})</span></label>
                <input type="text" name="payment_reference" placeholder="ex: VIREMENT-2026-05-18"
                    class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModals()" class="btn btn-secondary btn-sm">{{ __('Annuler') }}</button>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg mr-1"></i>{{ __('Confirmer le paiement') }}</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Rejeter --}}
<div id="reject-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md mx-4 p-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">{{ __('Rejeter la demande') }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __('Une note optionnelle sera visible par l\'affilié.') }}</p>
        <form id="reject-form" method="POST">
            @csrf
            <div class="mb-4">
                <label class="text-xs font-medium text-gray-700 dark:text-gray-300 block mb-1.5">{{ __('Motif') }} <span class="text-gray-400">({{ __('optionnel') }})</span></label>
                <input type="text" name="admin_note" placeholder="ex: Informations bancaires invalides"
                    class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-sm text-gray-900 dark:text-white px-3 py-2 focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModals()" class="btn btn-secondary btn-sm">{{ __('Annuler') }}</button>
                <button type="submit" class="btn btn-sm bg-red-600 text-white hover:bg-red-700"><i class="bi bi-x-lg mr-1"></i>{{ __('Rejeter') }}</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPayModal(id, amount) {
    document.getElementById('pay-modal-text').textContent = '{{ __("Confirmer le paiement de") }} ' + amount + ' {{ setting("currency_symbol", "€") }} ?';
    document.getElementById('pay-form').action = '{{ url(admin_prefix() . "/affiliation/withdrawals") }}/' + id + '/pay';
    document.getElementById('pay-modal').classList.remove('hidden');
}
function openRejectModal(id) {
    document.getElementById('reject-form').action = '{{ url(admin_prefix() . "/affiliation/withdrawals") }}/' + id + '/reject';
    document.getElementById('reject-modal').classList.remove('hidden');
}
function closeModals() {
    document.getElementById('pay-modal').classList.add('hidden');
    document.getElementById('reject-modal').classList.add('hidden');
}
document.getElementById('pay-modal').addEventListener('click', function(e) { if(e.target === this) closeModals(); });
document.getElementById('reject-modal').addEventListener('click', function(e) { if(e.target === this) closeModals(); });
</script>
@endsection
