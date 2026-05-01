@extends('admin.layouts.admin')

@section('title', __('Affilié') . ' #' . $affiliate->id)

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap items-center gap-4 mb-6">
    <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary btn-sm shrink-0">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-lg font-bold text-primary">
            {{ strtoupper(substr($affiliate->customer->firstname ?? $affiliate->customer->name ?? '?', 0, 1)) }}
        </div>
        <div class="min-w-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight truncate">
                {{ $affiliate->customer->firstname ?? '' }} {{ $affiliate->customer->lastname ?? ($affiliate->customer->name ?? '—') }}
            </h1>
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ $affiliate->customer->email ?? '' }} · {{ __('Affilié') }} #{{ $affiliate->id }}</p>
        </div>
    </div>
    <div class="flex gap-2 shrink-0">
        @if($affiliate->status === 'active')
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.active') }}
            </span>
        @elseif($affiliate->status === 'inactive')
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.inactive') }}
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.suspended') }}
            </span>
        @endif
        <a href="{{ route('affiliation.admin.edit', $affiliate) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil mr-1"></i>{{ __('Modifier') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-5 text-sm text-green-700 dark:text-green-300 flex items-center gap-2.5">
        <i class="bi bi-check-circle-fill text-base shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

{{-- Fiche + stats activité --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    {{-- Fiche affilié --}}
    <div class="card lg:col-span-2">
        <div class="card-heading">
            <div class="flex items-center gap-2">
                <i class="bi bi-person-vcard text-gray-400 dark:text-gray-500"></i>
                <h4>{{ __('Informations') }}</h4>
            </div>
        </div>
        <div class="card-body divide-y divide-gray-100 dark:divide-gray-700">
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Code de parrainage') }}</span>
                <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded-lg font-mono tracking-wider">{{ $affiliate->referral_code }}</code>
            </div>
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.stats.commission_rate') }}</span>
                <span class="font-bold text-gray-900 dark:text-white">{{ number_format($affiliate->commission_rate, 0) }}%</span>
            </div>
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.payment_method') }}</span>
                <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ $affiliate->payment_method }}</span>
            </div>
            @if($affiliate->payment_method === 'paypal' && !empty($affiliate->payment_details['paypal_email']))
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.paypal_email') }}</span>
                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $affiliate->payment_details['paypal_email'] }}</span>
            </div>
            @elseif($affiliate->payment_method === 'bank_transfer')
            @if(!empty($affiliate->payment_details['iban']))
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">IBAN</span>
                <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded-lg font-mono tracking-wider">{{ $affiliate->payment_details['iban'] }}</code>
            </div>
            @endif
            @if(!empty($affiliate->payment_details['bic']))
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">BIC / SWIFT</span>
                <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2.5 py-1 rounded-lg font-mono tracking-wider">{{ $affiliate->payment_details['bic'] }}</code>
            </div>
            @endif
            @endif
            @if($affiliate->approved_at)
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Approuvé le') }}</span>
                <span class="text-gray-700 dark:text-gray-300">{{ $affiliate->approved_at->format('d/m/Y') }}</span>
            </div>
            @endif
            <div class="flex justify-between items-center py-3 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Membre depuis') }}</span>
                <span class="text-gray-700 dark:text-gray-300">{{ $affiliate->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Stats activité --}}
    <div class="grid grid-cols-2 lg:grid-cols-1 gap-3 content-start">
        <div class="card" style="border-left: 4px solid #6b7280;">
            <div class="card-body flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                    <i class="bi bi-cursor text-gray-600 dark:text-gray-300 text-lg"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_clicks']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Clics') }}</p>
                </div>
            </div>
        </div>
        <div class="card" style="border-left: 4px solid #3b82f6;">
            <div class="card-body flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-person-check text-blue-600 dark:text-blue-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_referrals']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Parrainages') }}</p>
                </div>
            </div>
        </div>
        <div class="card lg:col-span-1 col-span-2" style="border-left: 4px solid #22c55e;">
            <div class="card-body flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-trophy text-green-600 dark:text-green-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['conversion_rate'], 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Conversion') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gains --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card" style="border-left: 4px solid #6b7280;">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <i class="bi bi-graph-up text-gray-600 dark:text-gray-300 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($affiliate->total_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Gains totaux') }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #eab308;">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format($affiliate->pending_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('En attente') }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #22c55e;">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($affiliate->paid_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Payé') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Commissions --}}
<div class="card mb-4">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <i class="bi bi-cash-stack text-gray-400 dark:text-gray-500"></i>
            <h4>{{ __('Affilix::affiliation.commissions') }}</h4>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-5 py-3">{{ __('Date') }}</th>
                    <th class="px-5 py-3">{{ __('Facture') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Montant') }}</th>
                    <th class="px-5 py-3">{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliate->commissions as $commission)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $commission->created_at->format('d/m/Y') }}</span>
                        <span class="block text-xs text-gray-400 dark:text-gray-500">{{ $commission->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @if($commission->invoice_id)
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md">#{{ $commission->invoice_id }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right whitespace-nowrap">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($commission->amount, 2) }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-0.5">{{ setting('currency_symbol', '€') }}</span>
                    </td>
                    <td class="px-5 py-3">
                        @if($commission->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.paid') }}
                            </span>
                        @elseif($commission->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.approved') }}
                            </span>
                        @elseif($commission->status === 'cancelled')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.cancelled') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.pending') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="flex flex-col items-center justify-center gap-3 py-12 w-full">
                            <div class="h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="bi bi-cash-stack text-2xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aucune commission') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Parrainages --}}
<div class="card mb-6">
    <div class="card-heading">
        <div class="flex items-center gap-2">
            <i class="bi bi-people text-gray-400 dark:text-gray-500"></i>
            <h4>{{ __('Affilix::affiliation.referrals') }}</h4>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-5 py-3">{{ __('Client parrainé') }}</th>
                    <th class="px-5 py-3">{{ __('Inscrit le') }}</th>
                    <th class="px-5 py-3">{{ __('Premier achat') }}</th>
                    <th class="px-5 py-3">{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliate->referrals as $referral)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-5 py-3 text-sm font-medium text-gray-800 dark:text-gray-200">
                        {{ $referral->customer->firstname ?? '' }} {{ $referral->customer->lastname ?? ($referral->customer->name ?? '—') }}
                    </td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $referral->registered_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-5 py-3 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $referral->first_purchase_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-5 py-3">
                        @if($referral->status === 'converted')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.converted') }}
                            </span>
                        @elseif($referral->status === 'registered')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.registered') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.clicked') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="flex flex-col items-center justify-center gap-3 py-12 w-full">
                            <div class="h-14 w-14 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="bi bi-people text-2xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aucun parrainage') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Zone danger --}}
<div class="rounded-xl border border-red-200 dark:border-red-800/50 bg-white dark:bg-gray-800 p-5 flex flex-wrap items-center justify-between gap-4">
    <div>
        <p class="text-sm font-semibold text-red-600 dark:text-red-400 flex items-center gap-1.5 mb-0.5">
            <i class="bi bi-exclamation-triangle-fill text-sm"></i>{{ __('Zone de danger') }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('La suppression est irréversible. Les commissions et parrainages associés seront également supprimés.') }}</p>
    </div>
    <form action="{{ route('affiliation.admin.destroy', $affiliate) }}" method="POST"
        onsubmit="return confirm('{{ __('Supprimer cet affilié ? Cette action est irréversible.') }}')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm bg-red-500 hover:bg-red-600 text-white border-0 shrink-0">
            <i class="bi bi-trash mr-1"></i>{{ __('Affilix::affiliation.admin.delete_affiliate') }}
        </button>
    </form>
</div>

</div>
@endsection
