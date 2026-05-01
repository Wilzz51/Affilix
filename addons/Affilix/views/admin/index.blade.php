@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.admin.manage_affiliates'))

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap justify-between items-start gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
            <i class="bi bi-people-fill text-primary"></i>{{ __('Affilix::affiliation.admin.manage_affiliates') }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ __('Suivez les performances et gérez vos affiliés') }}</p>
    </div>
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
    <div class="card" style="border-left: 4px solid #6b7280;">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center shrink-0">
                <i class="bi bi-people text-gray-600 dark:text-gray-300 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($stats['total_affiliates']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Total') }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #22c55e;">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle text-green-600 dark:text-green-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['active_affiliates']) }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Actifs') }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #3b82f6;">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-cash text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($stats['total_commissions'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('Commissions') }}</p>
            </div>
        </div>
    </div>
    <div class="card" style="border-left: 4px solid #eab308;">
        <div class="card-body flex items-center gap-3">
            <div class="h-11 w-11 rounded-xl bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center shrink-0">
                <i class="bi bi-hourglass-split text-yellow-600 dark:text-yellow-400 text-xl"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-yellow-500 dark:text-yellow-400">{{ number_format($stats['pending_commissions'], 2) }} {{ setting('currency_symbol', '€') }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-medium mt-0.5">{{ __('En attente') }}</p>
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
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700 flex flex-wrap gap-3 items-center">
            <div class="flex items-center gap-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 flex-1 min-w-[180px] max-w-xs">
                <i class="bi bi-search text-gray-400 text-xs shrink-0"></i>
                <input type="text" name="search" id="search-input"
                    value="{{ request('search') }}"
                    placeholder="{{ __('Nom, email, code…') }}"
                    class="bg-transparent text-sm text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none w-full">
            </div>

            <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-700/60 rounded-lg p-1">
                @foreach(['' => __('Tous'), 'active' => __('Affilix::affiliation.active'), 'inactive' => __('Affilix::affiliation.inactive'), 'suspended' => __('Affilix::affiliation.suspended')] as $val => $label)
                    @php $active = request('status', '') === $val; @endphp
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="{{ $val }}" class="sr-only status-filter" {{ $active ? 'checked' : '' }}>
                        <span class="px-3 py-1.5 text-xs font-medium rounded-md transition-colors block
                            {{ $active ? 'bg-white dark:bg-gray-600 text-gray-800 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
                            {{ $label }}
                        </span>
                    </label>
                @endforeach
            </div>

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
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-xs font-bold text-primary">
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
                        <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-md font-mono tracking-wider whitespace-nowrap">{{ $affiliate->referral_code }}</code>
                    </td>
                    <td class="px-5 py-4 text-sm font-medium text-center text-gray-700 dark:text-gray-300">{{ number_format($affiliate->commission_rate, 0) }}%</td>
                    <td class="px-5 py-4 text-sm text-center text-gray-700 dark:text-gray-300">{{ number_format($affiliate->total_referrals) }}</td>
                    <td class="px-5 py-4 text-right whitespace-nowrap">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($affiliate->total_earnings, 2) }}</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-0.5">{{ setting('currency_symbol', '€') }}</span>
                    </td>
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
                    <td colspan="8">
                        <div class="flex flex-col items-center justify-center gap-3 py-16 w-full">
                            <div class="h-16 w-16 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <i class="bi bi-people text-3xl text-gray-300 dark:text-gray-600"></i>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Aucun affilié trouvé') }}</p>
                                @if(request('search') || request('status'))
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Essayez de modifier vos filtres.') }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($affiliates->hasPages())
    <div class="px-6 py-3 border-t border-gray-100 dark:border-gray-700">
        {{ $affiliates->links('admin.shared.layouts.pagination') }}
    </div>
    @endif
</div>

</div>

<script>
document.querySelectorAll('input[name="status"]').forEach(radio => {
    radio.addEventListener('change', () => radio.closest('form').submit());
});

(function () {
    let timer;
    const input = document.getElementById('search-input');
    if (!input) return;
    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(() => this.closest('form').submit(), 400);
    });
})();
</script>
@endsection
