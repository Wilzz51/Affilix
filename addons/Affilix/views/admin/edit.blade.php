@extends('admin.layouts.admin')

@section('title', __('Affilix::affiliation.admin.edit_affiliate') . ' #' . $affiliate->id)

@section('content')
<div class="pt-4">

{{-- Header --}}
<div class="flex flex-wrap items-center gap-4 mb-6">
    <a href="{{ route('affiliation.admin.show', $affiliate) }}" class="btn btn-secondary btn-sm shrink-0">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <div class="h-11 w-11 rounded-full bg-primary/10 flex items-center justify-center shrink-0 text-base font-bold text-primary">
            {{ strtoupper(substr($affiliate->customer->firstname ?? $affiliate->customer->name ?? '?', 0, 1)) }}
        </div>
        <div class="min-w-0">
            <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                {{ $affiliate->customer->firstname ?? '' }} {{ $affiliate->customer->lastname ?? ($affiliate->customer->name ?? '—') }}
            </h1>
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ $affiliate->customer->email ?? '' }} · {{ __('Affilié') }} #{{ $affiliate->id }}</p>
        </div>
    </div>
    @if($affiliate->status === 'active')
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 shrink-0">
            <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.active') }}
        </span>
    @elseif($affiliate->status === 'inactive')
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 shrink-0">
            <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.inactive') }}
        </span>
    @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 shrink-0">
            <i class="bi bi-circle-fill text-[6px]"></i>{{ __('Affilix::affiliation.suspended') }}
        </span>
    @endif
</div>

@if($errors->any())
    <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 p-4 mb-5 text-sm text-red-700 dark:text-red-300 flex gap-2.5">
        <i class="bi bi-exclamation-circle-fill mt-0.5 shrink-0 text-base"></i>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Sidebar --}}
    <div class="space-y-4">

        {{-- Stats --}}
        <div class="card">
            <div class="card-heading">
                <div class="flex items-center gap-2">
                    <i class="bi bi-bar-chart text-gray-400 dark:text-gray-500 text-sm"></i>
                    <h4 class="text-sm">{{ __('Statistiques') }}</h4>
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700 text-sm">
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <i class="bi bi-upc-scan text-xs text-gray-400 dark:text-gray-500"></i>{{ __('Code') }}
                    </span>
                    <code class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-0.5 rounded-md font-mono tracking-wider">{{ $affiliate->referral_code }}</code>
                </div>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <i class="bi bi-cursor text-xs text-gray-400 dark:text-gray-500"></i>{{ __('Clics uniques') }}
                    </span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ number_format($affiliate->unique_clicks) }}</span>
                </div>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <i class="bi bi-people text-xs text-gray-400 dark:text-gray-500"></i>{{ __('Parrainages') }}
                    </span>
                    <div class="text-right">
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $affiliate->total_referrals }}</span>
                        @if($affiliate->successful_referrals > 0)
                            <span class="block text-xs text-green-500">{{ $affiliate->successful_referrals }} {{ __('conv.') }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between items-start px-4 py-3">
                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mt-0.5">
                        <i class="bi bi-graph-up text-xs text-gray-400 dark:text-gray-500"></i>{{ __('Gains') }}
                    </span>
                    <div class="text-right">
                        <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($affiliate->total_earnings, 2) }} {{ setting('currency_symbol', '€') }}</span>
                        <span class="block text-xs mt-0.5">
                            <span class="text-yellow-500">{{ number_format($affiliate->pending_earnings, 2) }}</span>
                            <span class="text-gray-300 dark:text-gray-600 mx-0.5">·</span>
                            <span class="text-green-500">{{ number_format($affiliate->paid_earnings, 2) }}</span>
                        </span>
                    </div>
                </div>
                <div class="flex justify-between items-center px-4 py-3">
                    <span class="text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                        <i class="bi bi-calendar3 text-xs text-gray-400 dark:text-gray-500"></i>{{ __('Membre depuis') }}
                    </span>
                    <span class="text-gray-600 dark:text-gray-400">{{ $affiliate->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Accès rapides --}}
        <div class="card">
            <div class="card-heading">
                <div class="flex items-center gap-2">
                    <i class="bi bi-lightning text-gray-400 dark:text-gray-500 text-sm"></i>
                    <h4 class="text-sm">{{ __('Accès rapides') }}</h4>
                </div>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <a href="{{ route('affiliation.admin.show', $affiliate) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <i class="bi bi-eye text-gray-400 dark:text-gray-500 w-4 text-center"></i>{{ __('Voir la fiche complète') }}
                    <i class="bi bi-arrow-right text-xs ml-auto text-gray-300 dark:text-gray-600"></i>
                </a>
                <a href="{{ route('affiliation.admin.commissions', ['affiliate_id' => $affiliate->id]) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <i class="bi bi-cash-stack text-gray-400 dark:text-gray-500 w-4 text-center"></i>{{ __('Ses commissions') }}
                    <i class="bi bi-arrow-right text-xs ml-auto text-gray-300 dark:text-gray-600"></i>
                </a>
                <a href="{{ url(admin_prefix() . '/customers/' . $affiliate->customer_id) }}"
                    class="flex items-center gap-3 px-4 py-3 text-sm text-gray-600 dark:text-gray-400 hover:text-primary dark:hover:text-primary hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <i class="bi bi-person text-gray-400 dark:text-gray-500 w-4 text-center"></i>{{ __('Profil client') }}
                    <i class="bi bi-arrow-right text-xs ml-auto text-gray-300 dark:text-gray-600"></i>
                </a>
            </div>
        </div>

    </div>

    {{-- Formulaire --}}
    <div class="lg:col-span-2">
        <div class="card">
            <div class="card-heading">
                <div class="flex items-center gap-2">
                    <i class="bi bi-pencil-square text-gray-400 dark:text-gray-500"></i>
                    <h4>{{ __('Modifier') }}</h4>
                </div>
            </div>
            <form action="{{ route('affiliation.admin.update', $affiliate) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="divide-y divide-gray-100 dark:divide-gray-700">

                    {{-- Taux de commission --}}
                    <div class="flex items-start justify-between gap-6 px-5 py-5">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ __('Taux de commission') }} <span class="text-red-500">*</span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ __('Appliqué sur chaque vente parrainée par cet affilié.') }}
                            </p>
                            @if(setting('default_commission_rate'))
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex items-center gap-1">
                                    <i class="bi bi-info-circle text-[11px]"></i>
                                    {{ __('Taux global par défaut :') }}
                                    <span class="font-medium text-gray-600 dark:text-gray-300">{{ setting('default_commission_rate') }}%</span>
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 shrink-0">
                            <input type="number" name="commission_rate"
                                min="0" max="100" step="0.01"
                                value="{{ old('commission_rate', $affiliate->commission_rate) }}"
                                class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-16 text-right"
                                required>
                            <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">%</span>
                        </div>
                    </div>

                    {{-- Rémunération au clic --}}
                    @php
                        use App\Addons\Affiliation\Models\AffiliationSetting;
                        $wasSubmitted   = request()->old() ? true : false;
                        $hasOverride    = $wasSubmitted
                            ? (old('click_override') === '1')
                            : ($affiliate->click_remuneration_enabled !== null);
                        $clickEnabled   = $wasSubmitted
                            ? (old('click_remuneration_enabled') === '1')
                            : ($affiliate->click_remuneration_enabled === true);
                        $clickRate      = old('click_remuneration_rate',
                            $affiliate->click_remuneration_rate ?? AffiliationSetting::get('click_remuneration_rate', '0.00'));
                        $globalEnabled  = AffiliationSetting::get('click_remuneration_enabled', '0') === '1';
                        $globalRate     = AffiliationSetting::get('click_remuneration_rate', '0.00');
                    @endphp
                    <div class="px-5 py-5">
                        <div class="flex items-start justify-between gap-6 mb-3">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('Rémunération au clic') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ __('Personnaliser la rémunération par clic unique pour cet affilié.') }}
                                </p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 flex items-center gap-1">
                                    <i class="bi bi-info-circle text-[11px]"></i>
                                    {{ __('Global :') }}
                                    @if($globalEnabled)
                                        <span class="text-green-500 font-medium">{{ __('Activé') }}</span>
                                        <span class="text-gray-300 dark:text-gray-600 mx-0.5">·</span>
                                        <span class="font-medium text-gray-600 dark:text-gray-300">{{ $globalRate }} {{ setting('currency_symbol', '€') }}/clic</span>
                                    @else
                                        <span class="text-yellow-500 font-medium">{{ __('Désactivé') }}</span>
                                    @endif
                                </p>
                            </div>
                            <label class="relative inline-flex items-center gap-2 cursor-pointer shrink-0">
                                <input type="checkbox" name="click_override" value="1" id="click-override"
                                    class="sr-only peer" {{ $hasOverride ? 'checked' : '' }}>
                                <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                                    peer-checked:bg-primary
                                    after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                                    after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                    peer-checked:after:translate-x-[18px]"></div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ __('Personnaliser') }}</span>
                            </label>
                        </div>

                        <div id="click-override-panel" class="{{ $hasOverride ? '' : 'hidden' }} bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 divide-y divide-gray-200 dark:divide-gray-600">
                            {{-- Activé/désactivé --}}
                            <div class="flex items-center justify-between px-4 py-3">
                                <div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Activé pour cet affilié') }}</span>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ __('Écrase le paramètre global.') }}</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" name="click_remuneration_enabled" value="1"
                                        class="sr-only peer" {{ $clickEnabled ? 'checked' : '' }}>
                                    <div class="w-10 h-[22px] rounded-full transition-colors bg-gray-200 dark:bg-gray-600
                                        peer-checked:bg-primary
                                        after:content-[''] after:absolute after:top-[3px] after:start-[3px]
                                        after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all
                                        peer-checked:after:translate-x-[18px]"></div>
                                </label>
                            </div>
                            {{-- Montant --}}
                            <div class="flex items-center justify-between px-4 py-3">
                                <div>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Montant par clic unique') }}</span>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ __('Laisser vide ou 0 pour utiliser le montant global.') }}</p>
                                </div>
                                <div class="flex items-center gap-2 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg px-3 h-9 shrink-0">
                                    <input type="number" name="click_remuneration_rate"
                                        min="0" max="999999" step="0.0001"
                                        value="{{ $clickRate }}"
                                        class="bg-transparent text-sm text-gray-900 dark:text-white border-0 outline-none ring-0 focus:outline-none focus:ring-0 w-20 text-right">
                                    <span class="text-sm text-gray-400 dark:text-gray-500 shrink-0">{{ setting('currency_symbol', '€') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Statut --}}
                    <div class="px-5 py-5">
                        <div class="flex items-start justify-between gap-6 mb-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ __('Affilix::affiliation.status') }} <span class="text-red-500">*</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ __('Passer à "Actif" enregistre automatiquement la date d\'approbation.') }}
                                </p>
                            </div>
                        </div>
                        @php
                            $currentStatus = old('status', $affiliate->status);
                            $cardBase = 'status-card flex flex-col items-center gap-1.5 p-4 rounded-xl border-2 transition-all cursor-pointer';
                            $cardOff  = 'border-gray-200 dark:border-gray-600 text-gray-400 dark:text-gray-500 hover:border-gray-300 dark:hover:border-gray-500';
                            $cardOn   = [
                                'active'    => 'border-green-500 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400',
                                'inactive'  => 'border-yellow-500 bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400',
                                'suspended' => 'border-red-500 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400',
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
                            $cardDesc = [
                                'active'    => __('Peut parrainer et générer des commissions'),
                                'inactive'  => __('Inscrit mais pas encore validé'),
                                'suspended' => __('Accès suspendu, aucune commission'),
                            ];
                        @endphp
                        <div class="grid grid-cols-3 gap-3" id="status-cards">
                            @foreach(['active', 'inactive', 'suspended'] as $st)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="{{ $st }}" class="sr-only status-radio"
                                        {{ $currentStatus === $st ? 'checked' : '' }}>
                                    <div data-status="{{ $st }}"
                                        data-on-classes="{{ $cardOn[$st] }}"
                                        data-off-classes="{{ $cardOff }}"
                                        class="{{ $cardBase }} {{ $currentStatus === $st ? $cardOn[$st] : $cardOff }}">
                                        <i class="bi {{ $cardIcon[$st] }} text-2xl"></i>
                                        <span class="text-xs font-semibold">{{ $cardLabel[$st] }}</span>
                                        <span class="text-[11px] text-center leading-tight opacity-70">{{ $cardDesc[$st] }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>

                <div class="flex justify-end gap-3 px-5 py-4 border-t border-gray-100 dark:border-gray-700">
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

document.getElementById('click-override').addEventListener('change', function () {
    document.getElementById('click-override-panel').classList.toggle('hidden', !this.checked);
});
</script>
@endsection
