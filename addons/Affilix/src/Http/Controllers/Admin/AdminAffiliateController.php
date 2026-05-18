<?php

namespace App\Addons\Affiliation\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Addons\Affiliation\Models\Affiliate;
use App\Addons\Affiliation\Models\AffiliateCommission;
use App\Addons\Affiliation\Models\AffiliateWithdrawal;
use App\Addons\Affiliation\Models\AffiliationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAffiliateController extends Controller
{
    /**
     * Liste des affiliés
     */
    public function index(Request $request)
    {
        $query = Affiliate::with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('firstname', 'like', '%' . $search . '%')
                       ->orWhere('lastname', 'like', '%' . $search . '%')
                       ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhere('referral_code', 'like', '%' . $search . '%');
            });
        }

        $sortable  = ['total_earnings', 'total_referrals', 'unique_clicks', 'created_at'];
        $sort      = in_array($request->sort, $sortable) ? $request->sort : 'created_at';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $affiliates = $query->orderBy($sort, $direction)->paginate(20)->withQueryString();

        $stats = [
            'total_affiliates'    => Affiliate::count(),
            'active_affiliates'   => Affiliate::where('status', 'active')->count(),
            'total_commissions'   => AffiliateCommission::sum('amount'),
            'pending_commissions' => AffiliateCommission::where('status', 'pending')->sum('amount'),
        ];

        return view('Affilix_admin::index', compact('affiliates', 'stats', 'sort', 'direction'));
    }

    public function exportCsv(Request $request)
    {
        $query = Affiliate::with('customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($cq) use ($search) {
                    $cq->where('firstname', 'like', '%' . $search . '%')
                       ->orWhere('lastname', 'like', '%' . $search . '%')
                       ->orWhere('email', 'like', '%' . $search . '%');
                })->orWhere('referral_code', 'like', '%' . $search . '%');
            });
        }

        $affiliates = $query->orderBy('created_at', 'desc')->get();
        $currency   = setting('currency_symbol', '€');
        $filename   = 'affilies-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($affiliates, $currency) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8 pour Excel

            fputcsv($handle, [
                'ID', 'Prénom', 'Nom', 'Email', 'Code parrainage',
                'Taux (%)', 'Clics uniques', 'Parrainages', 'Conversions',
                'Gains totaux (' . $currency . ')', 'En attente (' . $currency . ')', 'Payé (' . $currency . ')',
                'Statut', 'Inscrit le',
            ], ';');

            foreach ($affiliates as $affiliate) {
                fputcsv($handle, [
                    $affiliate->id,
                    $affiliate->customer->firstname ?? '',
                    $affiliate->customer->lastname ?? ($affiliate->customer->name ?? ''),
                    $affiliate->customer->email ?? '',
                    $affiliate->referral_code,
                    number_format($affiliate->commission_rate, 2),
                    $affiliate->unique_clicks,
                    $affiliate->total_referrals,
                    $affiliate->successful_referrals,
                    number_format($affiliate->total_earnings, 2),
                    number_format($affiliate->pending_earnings, 2),
                    number_format($affiliate->paid_earnings, 2),
                    $affiliate->status,
                    $affiliate->created_at->format('d/m/Y'),
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Afficher un affilié
     */
    public function show(Affiliate $affiliate)
    {
        $affiliate->load(['customer', 'referrals.customer', 'commissions.invoice']);

        $stats = [
            'total_clicks'         => $affiliate->clicks()->count(),
            'unique_clicks'        => $affiliate->unique_clicks,
            'total_referrals'      => $affiliate->total_referrals,
            'successful_referrals' => $affiliate->successful_referrals,
            'conversion_rate'      => $affiliate->getConversionRate(),
        ];

        // Clics par semaine (8 dernières semaines) — 1 requête
        $clicksRaw = $affiliate->clicks()
            ->where('created_at', '>=', now()->subWeeks(8)->startOfWeek())
            ->get(['created_at']);

        $clicksChart = collect(range(7, 0))->map(function ($i) use ($clicksRaw) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end   = now()->subWeeks($i)->endOfWeek();
            return [
                'label' => $start->format('d/m'),
                'count' => $clicksRaw->filter(fn($c) => $c->created_at->between($start, $end))->count(),
            ];
        });

        // Commissions par mois (6 derniers mois) — depuis la collection déjà chargée
        $commissionsChart = collect(range(5, 0))->map(function ($i) use ($affiliate) {
            $start = now()->subMonths($i)->startOfMonth();
            $end   = now()->subMonths($i)->endOfMonth();
            return [
                'label' => $start->format('M'),
                'total' => (float) $affiliate->commissions
                    ->filter(fn($c) => $c->created_at->between($start, $end))
                    ->sum('amount'),
            ];
        });

        return view('Affilix_admin::show', compact('affiliate', 'stats', 'clicksChart', 'commissionsChart'));
    }

    /**
     * Export CSV des commissions
     */
    public function exportCommissions(Request $request)
    {
        $query = AffiliateCommission::with(['affiliate.customer', 'referral.customer', 'invoice']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->affiliate_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->latest()->get();
        $currency    = setting('currency_symbol', '€');
        $filename    = 'commissions-' . now()->format('Y-m-d') . '.csv';

        return response()->stream(function () use ($commissions, $currency) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'ID', 'Date', 'Affilié', 'Email affilié', 'Client / Type',
                'Facture', 'Montant (' . $currency . ')', 'Taux (%)',
                'Statut', 'Approuvé le', 'Payé le', 'Référence paiement',
            ], ';');

            foreach ($commissions as $commission) {
                $affiliateName = trim(
                    ($commission->affiliate->customer->firstname ?? '') . ' ' .
                    ($commission->affiliate->customer->lastname ?? ($commission->affiliate->customer->name ?? ''))
                );
                $clientName = $commission->type === 'click' ? 'Clic' : trim(
                    ($commission->referral?->customer?->firstname ?? '') . ' ' .
                    ($commission->referral?->customer?->lastname ?? ($commission->referral?->customer?->name ?? ''))
                );

                fputcsv($handle, [
                    $commission->id,
                    $commission->created_at->format('d/m/Y H:i'),
                    $affiliateName,
                    $commission->affiliate->customer->email ?? '',
                    $clientName,
                    $commission->invoice_id ? '#' . $commission->invoice_id : '',
                    number_format($commission->amount, 2),
                    number_format($commission->commission_rate, 2),
                    $commission->status,
                    $commission->approved_at?->format('d/m/Y') ?? '',
                    $commission->paid_at?->format('d/m/Y') ?? '',
                    $commission->payment_reference ?? '',
                ], ';');
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Éditer un affilié
     */
    public function edit(Affiliate $affiliate)
    {
        $affiliate->load('customer');
        return view('Affilix_admin::edit', compact('affiliate'));
    }

    /**
     * Mettre à jour un affilié
     */
    public function update(Request $request, Affiliate $affiliate)
    {
        $request->validate([
            'commission_rate'      => 'required|numeric|min:0|max:100',
            'status'               => 'required|in:active,inactive,suspended',
            'click_remuneration_rate' => 'nullable|numeric|min:0|max:999999',
        ]);

        $data = $request->only(['commission_rate', 'status']);

        if ($request->status === 'active' && $affiliate->status !== 'active') {
            $data['approved_at'] = now();
        }

        if ($request->has('click_override')) {
            $data['click_remuneration_enabled'] = $request->boolean('click_remuneration_enabled');
            $data['click_remuneration_rate']    = (float) $request->input('click_remuneration_rate', 0);
        } else {
            $data['click_remuneration_enabled'] = null;
            $data['click_remuneration_rate']    = null;
        }

        $affiliate->update($data);

        return redirect()->route('affiliation.admin.show', $affiliate)
            ->with('success', __('Affilix::affiliation.admin.affiliate_updated'));
    }

    /**
     * Actions groupées sur plusieurs affiliés
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'affiliate_ids'   => 'required|array',
            'affiliate_ids.*' => 'exists:affiliates,id',
            'action'          => 'required|in:activate,suspend,delete',
        ]);

        $ids   = $request->affiliate_ids;
        $count = count($ids);

        switch ($request->action) {
            case 'activate':
                foreach (Affiliate::whereIn('id', $ids)->get() as $aff) {
                    $data = ['status' => 'active'];
                    if ($aff->status !== 'active') {
                        $data['approved_at'] = now();
                    }
                    $aff->update($data);
                }
                break;
            case 'suspend':
                Affiliate::whereIn('id', $ids)->update(['status' => 'suspended']);
                break;
            case 'delete':
                Affiliate::whereIn('id', $ids)->delete();
                break;
        }

        $messages = [
            'activate' => $count . ' affilié(s) activé(s).',
            'suspend'  => $count . ' affilié(s) suspendu(s).',
            'delete'   => $count . ' affilié(s) supprimé(s).',
        ];

        return redirect()->back()->with('success', $messages[$request->action]);
    }

    /**
     * Créer une commission manuelle
     */
    public function createCommission(Request $request, Affiliate $affiliate)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:0.01|max:99999',
            'description' => 'required|string|max:255',
        ]);

        $autoApprove = (bool) affiliation_setting('auto_approve_commissions', false);
        $status      = $autoApprove ? 'approved' : 'pending';

        DB::transaction(function () use ($request, $affiliate, $status, $autoApprove) {
            AffiliateCommission::create([
                'affiliate_id'    => $affiliate->id,
                'referral_id'     => null,
                'invoice_id'      => null,
                'amount'          => $request->amount,
                'commission_rate' => 0,
                'type'            => 'manual',
                'description'     => $request->description,
                'status'          => $status,
                'approved_at'     => $autoApprove ? now() : null,
            ]);

            $affiliate->increment('total_earnings', $request->amount);

            if ($autoApprove) {
                $affiliate->increment('pending_earnings', $request->amount);
            }
        });

        return redirect()->back()->with('success', __('Commission manuelle créée avec succès.'));
    }

    /**
     * Supprimer un affilié
     */
    public function destroy(Affiliate $affiliate)
    {
        $affiliate->delete();

        return redirect()->route('affiliation.admin.index')
            ->with('success', __('Affilix::affiliation.admin.affiliate_deleted'));
    }

    /**
     * Liste des commissions
     */
    public function commissions(Request $request)
    {
        $query = AffiliateCommission::with(['affiliate.customer', 'referral.customer', 'invoice']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->affiliate_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->latest()->paginate(20)->withQueryString();

        $affiliates = Affiliate::with('customer')->orderBy('created_at', 'desc')->get();

        $stats = [
            'pending'  => AffiliateCommission::where('status', 'pending')->sum('amount'),
            'approved' => AffiliateCommission::where('status', 'approved')->sum('amount'),
            'paid'     => AffiliateCommission::where('status', 'paid')->sum('amount'),
        ];

        return view('Affilix_admin::commissions', compact('commissions', 'stats', 'affiliates'));
    }

    /**
     * Annuler une commission individuelle
     */
    public function cancelCommission(AffiliateCommission $commission)
    {
        if (in_array($commission->status, ['pending', 'approved'])) {
            $commission->update(['status' => 'cancelled']);
        }

        return redirect()->back()->with('success', __('Commission annulée.'));
    }

    /**
     * Approuver des commissions
     */
    public function approveCommissions(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:affiliate_commissions,id',
        ]);

        $commissions = AffiliateCommission::whereIn('id', $request->commission_ids)
            ->where('status', 'pending')
            ->get();

        foreach ($commissions as $commission) {
            $commission->approve();
        }

        return redirect()->back()
            ->with('success', __('Affilix::affiliation.admin.commissions_approved', ['count' => count($commissions)]));
    }

    /**
     * Marquer des commissions comme payées
     */
    public function payCommissions(Request $request)
    {
        $request->validate([
            'commission_ids' => 'required|array',
            'commission_ids.*' => 'exists:affiliate_commissions,id',
            'payment_reference' => 'nullable|string|max:255',
        ]);

        $commissions = AffiliateCommission::whereIn('id', $request->commission_ids)
            ->where('status', 'approved')
            ->get();

        foreach ($commissions as $commission) {
            $commission->markAsPaid($request->payment_reference);
        }

        return redirect()->back()
            ->with('success', __('Affilix::affiliation.admin.commissions_paid', ['count' => count($commissions)]));
    }

    /**
     * Demandes de paiement
     */
    public function withdrawalRequests(Request $request)
    {
        $query = AffiliateWithdrawal::with('affiliate.customer');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'pending_count'  => AffiliateWithdrawal::where('status', 'pending')->count(),
            'pending_amount' => AffiliateWithdrawal::where('status', 'pending')->sum('amount'),
            'paid_total'     => AffiliateWithdrawal::where('status', 'paid')->sum('amount'),
        ];

        return view('Affilix_admin::withdrawals', compact('withdrawals', 'stats'));
    }

    public function payWithdrawal(Request $request, AffiliateWithdrawal $withdrawal)
    {
        $request->validate([
            'payment_reference' => 'nullable|string|max:255',
        ]);

        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', __('Cette demande n\'est pas en attente.'));
        }

        $withdrawal->pay($request->payment_reference);

        return redirect()->back()->with('success', __('Paiement effectué et commissions mises à jour.'));
    }

    public function rejectWithdrawal(Request $request, AffiliateWithdrawal $withdrawal)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        if ($withdrawal->status !== 'pending') {
            return redirect()->back()->with('error', __('Cette demande n\'est pas en attente.'));
        }

        $withdrawal->reject($request->admin_note);

        return redirect()->back()->with('success', __('Demande rejetée.'));
    }

    /**
     * Paramètres du système d'affiliation
     */
    public function settings()
    {
        return view('Affilix_admin::settings');
    }

    /**
     * Mettre à jour les paramètres
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'default_commission_rate' => 'required|numeric|min:0|max:100',
            'minimum_payout'          => 'required|numeric|min:0',
            'cookie_lifetime'         => 'required|integer|min:1|max:365',
            'click_remuneration_rate' => 'required|numeric|min:0|max:999999',
            'auto_payment_threshold'  => 'nullable|numeric|min:0.01',
            'auto_payment_frequency'  => 'nullable|in:daily,monthly',
        ]);

        // Checkboxes (non envoyées si décochées)
        $validated['auto_approve']                      = $request->has('auto_approve') ? '1' : '0';
        $validated['auto_approve_commissions']          = $request->has('auto_approve_commissions') ? '1' : '0';
        $validated['commission_first_order_only']       = $request->has('commission_first_order_only') ? '1' : '0';
        $validated['affiliation_payment_balance']       = $request->has('affiliation_payment_balance') ? '1' : '0';
        $validated['affiliation_payment_paypal']        = $request->has('affiliation_payment_paypal') ? '1' : '0';
        $validated['affiliation_payment_bank_transfer'] = $request->has('affiliation_payment_bank_transfer') ? '1' : '0';

        // Stockés dans affiliation_settings pour garantir la persistance indépendamment
        // du SettingsService ClientXCMS (qui peut ignorer les clés inconnues)
        AffiliationSetting::set('click_remuneration_enabled', $request->has('click_remuneration_enabled') ? '1' : '0');
        AffiliationSetting::set('click_remuneration_rate', (string) (float) $request->input('click_remuneration_rate', 0));
        AffiliationSetting::set('auto_payment_enabled', $request->has('auto_payment_enabled') ? '1' : '0');
        AffiliationSetting::set('auto_payment_frequency', $request->input('auto_payment_frequency', 'monthly'));
        AffiliationSetting::set('auto_payment_threshold', (string) max(0.01, (float) $request->input('auto_payment_threshold', 1)));

        unset($validated['click_remuneration_rate'], $validated['auto_payment_enabled'], $validated['auto_payment_frequency'], $validated['auto_payment_threshold']);

        \App\Models\Admin\Setting::updateSettings($validated);

        return redirect()->back()->with('success', __('Affilix::affiliation.admin.settings_saved'));
    }
}
