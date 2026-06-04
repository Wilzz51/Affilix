<?php

namespace App\Addons\Affiliation\Listeners;

use App\Events\Core\Invoice\InvoiceCompleted;
use App\Addons\Affiliation\Models\Affiliate;
use App\Addons\Affiliation\Models\Referral;
use App\Addons\Affiliation\Models\AffiliateCommission;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class CreateAffiliateCommission
{
    public function handle(InvoiceCompleted $event): void
    {
        $invoice  = $event->invoice;
        $customer = $invoice->customer;

        if (!$customer || $invoice->total <= 0) {
            return;
        }

        // Éviter de traiter deux fois la même facture
        if (AffiliateCommission::where('invoice_id', $invoice->id)->exists()) {
            return;
        }

        // 1. Chercher un parrainage existant (client inscrit via lien)
        $referral = Referral::where('customer_id', $customer->id)
            ->whereHas('affiliate', fn($q) => $q->where('status', 'active'))
            ->first();

        // 2. Si aucun parrainage, vérifier la session (client déjà inscrit qui clique le lien)
        if (!$referral) {
            $referralCode = Session::get('referral_code') ?? request()->cookie('referral_code');

            if (!$referralCode) {
                return;
            }

            $affiliate = Affiliate::where('referral_code', $referralCode)
                ->where('status', 'active')
                ->first();

            if (!$affiliate || $affiliate->customer_id === $customer->id) {
                return;
            }

            $referral = Referral::create([
                'affiliate_id'       => $affiliate->id,
                'customer_id'        => $customer->id,
                'referral_code'      => $referralCode,
                'ip_address'         => request()->ip(),
                'user_agent'         => request()->userAgent(),
                'clicked_at'         => now(),
                'registered_at'      => null,
                'first_purchase_at'  => now(),
                'status'             => 'converted',
            ]);

            $affiliate->increment('total_referrals');
            $affiliate->increment('successful_referrals');

            Session::forget('referral_code');
            Cookie::queue(Cookie::forget('referral_code'));
        } else {
            $affiliate = $referral->affiliate;

            if (!$referral->first_purchase_at) {
                $referral->markAsConverted();
            }
        }

        $isFirstOrder = !$referral->fresh()->first_purchase_at
            || !AffiliateCommission::where('referral_id', $referral->id)
                ->where('type', 'sale')
                ->whereNotIn('status', ['cancelled'])
                ->exists();

        if (affiliation_setting('commission_first_order_only', false)) {
            $alreadyCommissioned = AffiliateCommission::where('referral_id', $referral->id)
                ->whereNotIn('status', ['cancelled'])
                ->exists();
            if ($alreadyCommissioned) {
                return;
            }
        }

        $foGlobalEnabled = \App\Addons\Affiliation\Models\AffiliationSetting::get('first_order_commission_enabled', '0') === '1';
        $foAffiliate     = $affiliate->first_order_commission_rate !== null;

        // Priorité 1a : premier achat avec taux par affilié
        if ($isFirstOrder && $foAffiliate) {
            $rate = (float) $affiliate->first_order_commission_rate;
            $type = $affiliate->first_order_commission_type ?? 'percent';
        }
        // Priorité 1b : premier achat avec taux global
        elseif ($isFirstOrder && $foGlobalEnabled) {
            $rate = (float) \App\Addons\Affiliation\Models\AffiliationSetting::get('first_order_commission_rate', '0');
            $type = \App\Addons\Affiliation\Models\AffiliationSetting::get('first_order_commission_type', 'percent');
        }
        // Priorité 1c : achats suivants avec taux par affilié
        elseif (!$isFirstOrder && $foAffiliate && $affiliate->after_first_order_commission_rate !== null) {
            $rate = (float) $affiliate->after_first_order_commission_rate;
            $type = $affiliate->after_first_order_commission_type ?? 'percent';
        }
        // Priorité 1d : achats suivants avec taux global (peut être 0)
        elseif (!$isFirstOrder && $foGlobalEnabled) {
            $rate = (float) \App\Addons\Affiliation\Models\AffiliationSetting::get('after_first_order_commission_rate', '0');
            $type = \App\Addons\Affiliation\Models\AffiliationSetting::get('after_first_order_commission_type', 'percent');
        }
        // Priorité 2 : paliers de commission
        elseif (\App\Addons\Affiliation\Models\AffiliationSetting::get('commission_tiers_enabled', '0') === '1') {
            $tiers  = json_decode(\App\Addons\Affiliation\Models\AffiliationSetting::get('commission_tiers', '[]'), true) ?: [];
            $metric = \App\Addons\Affiliation\Models\AffiliationSetting::get('commission_tiers_metric', 'successful_referrals');
            $value  = (int) ($affiliate->{$metric} ?? 0);

            usort($tiers, fn($a, $b) => (int)$b['threshold'] <=> (int)$a['threshold']);
            $matched = null;
            foreach ($tiers as $tier) {
                if ($value >= (int) $tier['threshold']) {
                    $matched = $tier;
                    break;
                }
            }
            $rate = $matched ? (float) $matched['rate'] : (float) $affiliate->commission_rate;
            $type = $matched ? ($matched['type'] ?? 'percent') : ($affiliate->commission_type ?? 'percent');
        }
        // Priorité 3 : taux habituel de l'affilié
        else {
            $rate = (float) $affiliate->commission_rate;
            $type = $affiliate->commission_type ?? 'percent';
        }

        $commissionAmount = $type === 'fixed'
            ? round($rate, 2)
            : round(($invoice->total * $rate) / 100, 2);

        if ($commissionAmount <= 0) {
            return;
        }

        $autoApprove = (bool) affiliation_setting('auto_approve_commissions', false);

        AffiliateCommission::create([
            'affiliate_id'    => $affiliate->id,
            'referral_id'     => $referral->id,
            'invoice_id'      => $invoice->id,
            'amount'          => $commissionAmount,
            'commission_rate' => $affiliate->commission_rate,
            'description'     => __('Affilix::affiliation.commission_description', ['id' => $invoice->id]),
            'status'          => $autoApprove ? 'approved' : 'pending',
            'approved_at'     => $autoApprove ? now() : null,
        ]);

        $affiliate->increment('total_earnings', $commissionAmount);

        if ($autoApprove) {
            $affiliate->increment('pending_earnings', $commissionAmount);
        }
    }
}
