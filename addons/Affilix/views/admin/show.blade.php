@extends('admin.layouts.admin')

@section('title', __('Affilié') . ' #' . $affiliate->id)

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('affiliation.admin.index') }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
            {{ $affiliate->customer->firstname ?? '' }} {{ $affiliate->customer->lastname ?? ($affiliate->customer->name ?? '—') }}
        </h1>
        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Affilié') }} #{{ $affiliate->id }} · {{ $affiliate->customer->email ?? '' }}</p>
    </div>
    <div class="ml-auto flex gap-2">
        <a href="{{ route('affiliation.admin.edit', $affiliate) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-pencil mr-1"></i>{{ __('Modifier') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 p-4 mb-4 text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
        <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
    </div>
@endif

{{-- Infos + stats --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
    {{-- Fiche affilié --}}
    <div class="card lg:col-span-2">
        <div class="card-heading">
            <h4>{{ __('Informations') }}</h4>
            @if($affiliate->status === 'active')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                    <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.active') }}
                </span>
            @elseif($affiliate->status === 'inactive')
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                    <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.inactive') }}
                </span>
            @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                    <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.suspended') }}
                </span>
            @endif
        </div>
        <div class="card-body divide-y divide-gray-100 dark:divide-gray-700">
            <div class="flex justify-between py-2.5 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Code de parrainage') }}</span>
                <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-1.5 py-0.5 rounded font-mono">{{ $affiliate->referral_code }}</code>
            </div>
            <div class="flex justify-between py-2.5 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.stats.commission_rate') }}</span>
                <span class="font-semibold">{{ number_format($affiliate->commission_rate, 0) }}%</span>
            </div>
            <div class="flex justify-between py-2.5 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Affilix::affiliation.payment_method') }}</span>
                <span class="font-medium capitalize">{{ $affiliate->payment_method }}</span>
            </div>
            @if($affiliate->approved_at)
            <div class="flex justify-between py-2.5 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Approuvé le') }}</span>
                <span>{{ $affiliate->approved_at->format('d/m/Y') }}</span>
            </div>
            @endif
            <div class="flex justify-between py-2.5 text-sm">
                <span class="text-gray-500 dark:text-gray-400">{{ __('Membre depuis') }}</span>
                <span>{{ $affiliate->created_at->format('d/m/Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Stats activité --}}
    <div class="grid grid-cols-2 lg:grid-cols-1 gap-3 content-start">
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                    <i class="bi bi-cursor text-gray-600 dark:text-gray-300"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_clicks']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Clics') }}</p>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-person-check text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_referrals']) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Parrainages') }}</p>
                </div>
            </div>
        </div>
        <div class="card lg:col-span-1 col-span-2">
            <div class="card-body flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                    <i class="bi bi-trophy text-green-600 dark:text-green-400"></i>
                </div>
                <div>
                    <p class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['conversion_rate'], 1) }}%</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Conversion') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Gains --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card">
        <div class="card-body flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <i class="bi bi-graph-up text-gray-600 dark:text-gray-300 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($affiliate->total_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Gains totaux') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format($affiliate->pending_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('En attente de paiement') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($affiliate->paid_earnings, 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Payé') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Commissions --}}
<div class="card mb-4">
    <div class="card-heading">
        <h4>{{ __('Affilix::affiliation.commissions') }}</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Date') }}</th>
                    <th>{{ __('Facture') }}</th>
                    <th>{{ __('Montant') }}</th>
                    <th>{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliate->commissions as $commission)
                <tr>
                    <td class="text-sm text-gray-500 dark:text-gray-400">{{ $commission->created_at->format('d/m/Y') }}</td>
                    <td>
                        @if($commission->invoice_id)
                            <span class="font-mono text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded">#{{ $commission->invoice_id }}</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="text-sm font-semibold">{{ number_format($commission->amount, 2) }} {{ setting('currency_symbol', '€') }}</td>
                    <td>
                        @if($commission->status === 'paid')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.paid') }}
                            </span>
                        @elseif($commission->status === 'approved')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.approved') }}
                            </span>
                        @elseif($commission->status === 'cancelled')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.cancelled') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.pending') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center">
                        <i class="bi bi-cash-stack text-3xl text-gray-300 dark:text-gray-600 block mb-2"></i>
                        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucune commission') }}</p>
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
        <h4>{{ __('Affilix::affiliation.referrals') }}</h4>
    </div>
    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th>{{ __('Client parrainé') }}</th>
                    <th>{{ __('Inscrit le') }}</th>
                    <th>{{ __('Premier achat') }}</th>
                    <th>{{ __('Statut') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliate->referrals as $referral)
                <tr>
                    <td class="text-sm font-medium">
                        {{ $referral->customer->firstname ?? '' }} {{ $referral->customer->lastname ?? ($referral->customer->name ?? '—') }}
                    </td>
                    <td class="text-sm text-gray-500 dark:text-gray-400">{{ $referral->registered_at?->format('d/m/Y') ?? '—' }}</td>
                    <td class="text-sm text-gray-500 dark:text-gray-400">{{ $referral->first_purchase_at?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                        @if($referral->status === 'converted')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.converted') }}
                            </span>
                        @elseif($referral->status === 'registered')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.registered') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.clicked') }}
                            </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-10 text-center">
                        <i class="bi bi-people text-3xl text-gray-300 dark:text-gray-600 block mb-2"></i>
                        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucun parrainage') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Suppression --}}
<div class="card border border-red-200 dark:border-red-800/50">
    <div class="card-body flex items-center justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Supprimer cet affilié') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ __('Cette action est irréversible. Les commissions et parrainages associés seront également supprimés.') }}</p>
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

</div>
@endsection
