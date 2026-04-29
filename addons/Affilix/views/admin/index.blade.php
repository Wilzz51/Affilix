@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.admin.manage_affiliates'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
        <i class="bi bi-people-fill mr-2 text-primary"></i>{{ __('Affilix::affiliation.admin.manage_affiliates') }}
    </h1>
    <div class="flex gap-2">
        <a href="{{ route('affiliation.admin.commissions') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-cash-stack mr-1"></i>{{ __('Commissions') }}
        </a>
        <a href="{{ route('affiliation.admin.settings') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-gear mr-1"></i>{{ __('Affilix::affiliation.settings') }}
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card">
        <div class="card-body flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <i class="bi bi-people text-gray-600 dark:text-gray-300 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_affiliates']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Total') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['active_affiliates']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Actifs') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-cash text-blue-600 dark:text-blue-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_commissions'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('Commissions') }}</p>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-lg"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format($stats['pending_commissions'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('En attente') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Tableau --}}
<div class="card">
    <div class="card-heading">
        <h4>{{ __('Affilix::affiliation.admin.affiliates') }}</h4>
    </div>

    {{-- Barre de filtres --}}
    <form action="{{ route('affiliation.admin.index') }}" method="GET" id="filter-form">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 flex flex-wrap gap-3 items-center">
            {{-- Recherche --}}
            <div class="flex items-center gap-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-8 flex-1 min-w-[180px] max-w-xs">
                <i class="bi bi-search text-gray-400 text-xs shrink-0"></i>
                <input type="text" name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('Nom, email, code…') }}"
                    class="bg-transparent text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none w-full">
            </div>

            {{-- Filtres statut --}}
            <div class="flex items-center gap-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-0.5">
                @foreach(['' => __('Tous'), 'active' => __('Affilix::affiliation.active'), 'inactive' => __('Affilix::affiliation.inactive'), 'suspended' => __('Affilix::affiliation.suspended')] as $val => $label)
                    @php $active = request('status', '') === $val; @endphp
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="{{ $val }}" class="sr-only status-filter"
                            {{ $active ? 'checked' : '' }}>
                        <span class="px-3 py-1 text-xs font-medium rounded-md transition-colors block
                            {{ $active ? 'bg-primary text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="btn btn-sm btn-primary">{{ __('Filtrer') }}</button>

            @if(request('search') || request('status'))
                <a href="{{ route('affiliation.admin.index') }}"
                    class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 flex items-center gap-1">
                    <i class="bi bi-x-circle"></i>{{ __('Réinitialiser') }}
                </a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="table">
            <thead>
                <tr>
                    <th class="px-5 py-3">{{ __('Client') }}</th>
                    <th class="px-5 py-3">{{ __('Code') }}</th>
                    <th class="px-5 py-3 text-center">{{ __('Taux') }}</th>
                    <th class="px-5 py-3 text-center">{{ __('Parrainages') }}</th>
                    <th class="px-5 py-3 text-right">{{ __('Gains') }}</th>
                    <th class="px-5 py-3">{{ __('Statut') }}</th>
                    <th class="px-5 py-3">{{ __('Depuis') }}</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($affiliates as $affiliate)
                <tr>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-xs font-bold text-primary">
                                {{ strtoupper(substr($affiliate->customer->firstname ?? $affiliate->customer->name ?? '?', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <a href="{{ url('/admin/customers/' . $affiliate->customer_id) }}"
                                    class="text-sm font-medium text-primary hover:underline block truncate max-w-[180px]">
                                    {{ $affiliate->customer->firstname ?? '' }} {{ $affiliate->customer->lastname ?? ($affiliate->customer->name ?? '—') }}
                                </a>
                                <p class="text-xs text-gray-400 dark:text-gray-500 truncate max-w-[180px]">{{ $affiliate->customer->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded font-mono tracking-wider whitespace-nowrap">{{ $affiliate->referral_code }}</code>
                    </td>
                    <td class="px-5 py-4 text-sm font-medium text-center">{{ number_format($affiliate->commission_rate, 0) }}%</td>
                    <td class="px-5 py-4 text-sm text-center">{{ number_format($affiliate->total_referrals) }}</td>
                    <td class="px-5 py-4 text-sm font-semibold text-right whitespace-nowrap">{{ number_format($affiliate->total_earnings, 2) }} {{ setting('currency_symbol', '€') }}</td>
                    <td class="px-5 py-4">
                        @if($affiliate->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.active') }}
                            </span>
                        @elseif($affiliate->status === 'inactive')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.inactive') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">
                                <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.suspended') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $affiliate->created_at->format('d/m/Y') }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5 justify-end">
                            <a href="{{ route('affiliation.admin.show', $affiliate) }}"
                                class="btn btn-sm btn-secondary" title="{{ __('Voir') }}">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('affiliation.admin.edit', $affiliate) }}"
                                class="btn btn-sm btn-primary" title="{{ __('Modifier') }}">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-14 text-center">
                        <i class="bi bi-people text-4xl text-gray-300 dark:text-gray-600 block mb-2"></i>
                        <p class="text-sm text-gray-400 dark:text-gray-500">{{ __('Aucun affilié trouvé') }}</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-3">
        {{ $affiliates->links('admin.shared.layouts.pagination') }}
    </div>
</div>

</div>

<script>
// Soumettre automatiquement le formulaire au clic sur un radio statut
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', () => radio.closest('form').submit());
});
</script>
@endsection
