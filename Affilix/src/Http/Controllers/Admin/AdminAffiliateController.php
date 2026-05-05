<?php

namespace App\Addons\Affiliation\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Addons\Affiliation\Models\Affiliate;
use App\Addons\Affiliation\Models\AffiliateCommission;
use App\Addons\Affiliation\Models\AffiliationSetting;
use Illuminate\Http\Request;

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

        $affiliates = $query->latest()->paginate(20);

        // Statistiques globales
        $stats = [
            'total_affiliates' => Affiliate::count(),
            'active_affiliates' => Affiliate::where('status', 'active')->count(),
            'total_commissions' => AffiliateCommission::sum('amount'),
            'pending_commissions' => AffiliateCommission::where('status', 'pending')->sum('amount'),
        ];

        return view('Affilix_admin::index', compact('affiliates', 'stats'));
    }

    /**
     * Afficher un affilié
     */
    public function show(Affiliate $affiliate)
    {
        $affiliate->load(['customer', 'referrals.customer', 'commissions.invoice']);

        $stats = [
            'total_clicks'        => $affiliate->clicks()->count(),
            'unique_clicks'       => $affiliate->unique_clicks,
            'total_referrals'     => $affiliate->total_referrals,
            'successful_referrals'=> $affiliate->successful_referrals,
            'conversion_rate'     => $affiliate->getConversionRate(),
        ];

        return view('Affilix_admin::show', compact('affiliate', 'stats'));
    }

    /**
     * Éditer un affilié
     */
    public function edit(Affiliate $affiliate)
    {
        return view('Affilix_admin::edit', compact('affiliate'));
    }

    /**
     * Mettre à jour un affilié
     */
    public function update(Request $request, Affiliate $affiliate)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $data = $request->only(['commission_rate', 'status']);

        if ($request->status === 'active' && $affiliate->status !== 'active') {
            $data['approved_at'] = now();
        }

        $affiliate->update($data);

        return redirect()->route('affiliation.admin.show', $affiliate)
            ->with('success', __('Affilix::affiliation.admin.affiliate_updated'));
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

        // Filtres
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->affiliate_id);
        }

        $commissions = $query->latest()->paginate(20);

        $stats = [
            'pending' => AffiliateCommission::where('status', 'pending')->sum('amount'),
            'approved' => AffiliateCommission::where('status', 'approved')->sum('amount'),
            'paid' => AffiliateCommission::where('status', 'paid')->sum('amount'),
        ];

        return view('Affilix_admin::commissions', compact('commissions', 'stats'));
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
        ]);

        // Checkboxes (non envoyées si décochées)
        $validated['auto_approve']                      = $request->has('auto_approve') ? '1' : '0';
        $validated['auto_approve_commissions']          = $request->has('auto_approve_commissions') ? '1' : '0';
        $validated['commission_first_order_only']       = $request->has('commission_first_order_only') ? '1' : '0';
        $validated['affiliation_payment_balance']       = $request->has('affiliation_payment_balance') ? '1' : '0';
        $validated['affiliation_payment_paypal']        = $request->has('affiliation_payment_paypal') ? '1' : '0';
        $validated['affiliation_payment_bank_transfer'] = $request->has('affiliation_payment_bank_transfer') ? '1' : '0';

        // Les settings décimaux/booléens du clic sont stockés dans affiliation_settings
        // pour éviter la conversion int automatique du SettingsService de ClientXCMS
        AffiliationSetting::set('click_remuneration_enabled', $request->has('click_remuneration_enabled') ? '1' : '0');
        AffiliationSetting::set('click_remuneration_rate', (string) (float) $request->input('click_remuneration_rate', 0));
        unset($validated['click_remuneration_rate']);

        \App\Models\Admin\Setting::updateSettings($validated);

        return redirect()->back()->with('success', __('Affilix::affiliation.admin.settings_saved'));
    }
}
