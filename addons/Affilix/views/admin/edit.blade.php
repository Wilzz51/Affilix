@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.admin.edit_affiliate') . ' #' . $affiliate->id)

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('affiliation.admin.show', $affiliate) }}" class="btn btn-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">
            {{ __('Affilix::affiliation.admin.edit_affiliate') }}
        </h1>
        <p class="text-sm text-gray-400 dark:text-gray-500">
            {{ $affiliate->customer->firstname ?? '' }} {{ $affiliate->customer->lastname ?? ($affiliate->customer->name ?? '') }} · #{{ $affiliate->id }}
        </p>
    </div>
</div>

@if($errors->any())
    <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 mb-4 text-sm text-red-700 dark:text-red-300 flex gap-2">
        <i class="bi bi-exclamation-circle-fill mt-0.5 shrink-0"></i>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 max-w-4xl">

    {{-- Panneau info (lecture seule) --}}
    <div class="space-y-4">
        {{-- Avatar + nom --}}
        <div class="card">
            <div class="card-body flex items-center gap-3">
                <div class="h-12 w-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-lg font-bold text-primary">
                    {{ strtoupper(substr($affiliate->customer->firstname ?? $affiliate->customer->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 dark:text-white truncate">
                        {{ $affiliate->customer->firstname ?? '' }} {{ $affiliate->customer->lastname ?? ($affiliate->customer->name ?? '—') }}
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $affiliate->customer->email ?? '' }}</p>
                </div>
            </div>
        </div>

        {{-- Infos lecture seule --}}
        <div class="card">
            <div class="card-heading"><h4 class="text-sm">{{ __('Infos') }}</h4></div>
            <div class="card-body divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                <div class="flex justify-between py-2.5">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Code') }}</span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded font-mono tracking-wider">{{ $affiliate->referral_code }}</code>
                </div>
                <div class="flex justify-between py-2.5">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Parrainages') }}</span>
                    <span class="font-medium">{{ $affiliate->total_referrals }}</span>
                </div>
                <div class="flex justify-between py-2.5">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Gains totaux') }}</span>
                    <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($affiliate->total_earnings, 2) }} {{ setting('currency_symbol', '€') }}</span>
                </div>
                <div class="flex justify-between py-2.5">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('Membre depuis') }}</span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $affiliate->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-heading">
                <h4>{{ __('Modifier') }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('affiliation.admin.update', $affiliate) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-5">

                        {{-- Taux de commission --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ __('Affilix::affiliation.stats.commission_rate') }}
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2 max-w-xs">
                                <input type="number" name="commission_rate"
                                    min="0" max="100" step="0.01"
                                    value="{{ old('commission_rate', $affiliate->commission_rate) }}"
                                    class="input w-24" required>
                                <span class="text-sm text-gray-500 dark:text-gray-400">%</span>
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('Entre 0 et 100. Appliqué sur chaque vente parrainée.') }}</p>
                        </div>

                        {{-- Statut --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Affilix::affiliation.status') }}
                                <span class="text-red-500">*</span>
                            </label>
                            @php
                                $currentStatus = old('status', $affiliate->status);
                                $cardBase = 'status-card flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition-all';
                                $cardOff  = 'border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500 hover:border-gray-300 dark:hover:border-gray-500';
                                $cardOn   = [
                                    'active'    => 'border-green-500 bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400',
                                    'inactive'  => 'border-yellow-500 bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400',
                                    'suspended' => 'border-red-500 bg-red-100 text-red-600 dark:bg-red-900/20 dark:text-red-400',
                                ];
                                $cardIcon = [
                                    'active'    => 'bi-check-circle-fill',
                                    'inactive'  => 'bi-pause-circle-fill',
                                    'suspended' => 'bi-x-circle-fill',
                                ];
                                $cardLabel = [
                                    'active'    => __('Affilix::affiliation.active'),
                                    'inactive'  => __('Affilix::affiliation.inactive'),
                                    'suspended' => __('Affilix::affiliation.suspended'),
                                ];
                            @endphp
                            <div class="grid grid-cols-3 gap-2" id="status-cards">
                                @foreach(['active', 'inactive', 'suspended'] as $st)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="status" value="{{ $st }}" class="sr-only status-radio"
                                            {{ $currentStatus === $st ? 'checked' : '' }}>
                                        <div data-status="{{ $st }}"
                                            data-on-classes="{{ $cardOn[$st] }}"
                                            data-off-classes="{{ $cardOff }}"
                                            class="{{ $cardBase }} {{ $currentStatus === $st ? $cardOn[$st] : $cardOff }}">
                                            <i class="bi {{ $cardIcon[$st] }} text-2xl"></i>
                                            <span class="text-xs font-medium">{{ $cardLabel[$st] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ __('Passer à "Actif" enregistre la date d\'approbation.') }}</p>
                        </div>

                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-5 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('affiliation.admin.show', $affiliate) }}" class="btn btn-secondary">{{ __('Annuler') }}</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg mr-1"></i>{{ __('Enregistrer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
</div>

<script>
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
