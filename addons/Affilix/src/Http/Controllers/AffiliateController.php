<?php

namespace App\Addons\Affiliation\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Addons\Affiliation\Models\Affiliate;
use App\Addons\Affiliation\Models\AffiliateClick;
use App\Addons\Affiliation\Models\AffiliateCommission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    private function getAffiliate(): ?Affiliate
    {
        return Affiliate::where('customer_id', Auth::id())->first();
    }

    public function dashboard()
    {
        $affiliate = $this->getAffiliate();

        if (!$affiliate) {
            return redirect()->route('affiliation.register');
        }

        $stats = [
            'total_clicks'        => $affiliate->unique_clicks,
            'unique_clicks'       => $affiliate->unique_clicks,
            'total_referrals'     => $affiliate->total_referrals,
            'successful_referrals'=> $affiliate->successful_referrals,
            'conversion_rate'     => $affiliate->getConversionRate(),
            'total_earnings'      => $affiliate->total_earnings,
            'pending_earnings'    => $affiliate->pending_earnings,
            'paid_earnings'       => $affiliate->paid_earnings,
        ];

        $recentCommissions = $affiliate->commissions()
            ->with(['invoice', 'referral.customer'])
            ->latest()
            ->take(10)
            ->get();

        $recentReferrals = $affiliate->referrals()
            ->with('customer')
            ->latest()
            ->take(10)
            ->get();

        return view('Affilix::dashboard', compact(
            'affiliate',
            'stats',
            'recentCommissions',
            'recentReferrals'
        ));
    }

    public function register()
    {
        if ($this->getAffiliate()) {
            return redirect()->route('affiliation.dashboard');
        }

        return view('Affilix::register');
    }

    public function store(Request $request)
    {
        if ($this->getAffiliate()) {
            return redirect()->route('affiliation.dashboard')
                ->with('info', __('Affilix::affiliation.messages.already_registered'));
        }

        $rules = ['payment_method' => 'required|in:balance,paypal,bank_transfer'];

        if ($request->payment_method === 'paypal') {
            $rules['payment_details.paypal_email'] = 'required|email|max:255';
        } elseif ($request->payment_method === 'bank_transfer') {
            $rules['payment_details.iban'] = 'required|string|max:34';
            $rules['payment_details.bic']  = 'nullable|string|max:11';
        }

        $request->validate($rules);

        $affiliate = Affiliate::create([
            'customer_id'     => Auth::id(),
            'referral_code'   => Affiliate::generateReferralCode(),
            'commission_rate' => affiliation_setting('default_commission_rate', 10),
            'payment_method'  => $request->payment_method,
            'payment_details' => $request->payment_details ?? [],
            'status'          => affiliation_setting('auto_approve', true) ? 'active' : 'inactive',
            'approved_at'     => affiliation_setting('auto_approve', true) ? now() : null,
        ]);

        $message = $affiliate->status === 'active'
            ? __('Affilix::affiliation.messages.registered_success')
            : __('Affilix::affiliation.messages.registered_pending');

        return redirect()->route('affiliation.dashboard')->with('success', $message);
    }

    public function commissions()
    {
        $affiliate = $this->getAffiliate();

        if (!$affiliate) {
            return redirect()->route('affiliation.register');
        }

        $commissions = $affiliate->commissions()
            ->with(['invoice', 'referral.customer'])
            ->latest()
            ->paginate(20);

        return view('Affilix::commissions', compact('affiliate', 'commissions'));
    }

    public function referrals()
    {
        $affiliate = $this->getAffiliate();

        if (!$affiliate) {
            return redirect()->route('affiliation.register');
        }

        $referrals = $affiliate->referrals()
            ->with(['customer', 'commissions'])
            ->latest()
            ->paginate(20);

        return view('Affilix::referrals', compact('affiliate', 'referrals'));
    }

    public function settings()
    {
        $affiliate = $this->getAffiliate();

        if (!$affiliate) {
            return redirect()->route('affiliation.register');
        }

        return view('Affilix::settings', compact('affiliate'));
    }

    public function updateSettings(Request $request)
    {
        $affiliate = $this->getAffiliate();

        if (!$affiliate) {
            return redirect()->route('affiliation.register');
        }

        $rules = ['payment_method' => 'required|in:balance,paypal,bank_transfer'];

        if ($request->payment_method === 'paypal') {
            $rules['payment_details.paypal_email'] = 'required|email|max:255';
        } elseif ($request->payment_method === 'bank_transfer') {
            $rules['payment_details.iban'] = 'required|string|max:34';
            $rules['payment_details.bic']  = 'nullable|string|max:11';
        }

        $request->validate($rules);

        $affiliate->update([
            'payment_method'  => $request->payment_method,
            'payment_details' => $request->payment_details ?? [],
        ]);

        return redirect()->route('affiliation.settings')
            ->with('success', __('Affilix::affiliation.messages.settings_updated'));
    }

    public function trackClick(Request $request, string $code)
    {
        $affiliate = Affiliate::where('referral_code', $code)
            ->where('status', 'active')
            ->first();

        if (!$affiliate) {
            return redirect('/');
        }

        $click = AffiliateClick::trackClick($affiliate);

        if ($click->is_unique) {
            $this->createClickCommission($affiliate);
        }

        $lifetime = (int) affiliation_setting('cookie_lifetime', 30);
        session(['referral_code' => $code]);
        Cookie::queue('referral_code', $code, $lifetime * 24 * 60);

        return redirect('/');
    }

    private function createClickCommission(Affiliate $affiliate): void
    {
        if (affiliation_setting('click_remuneration_enabled', '0') !== '1') {
            return;
        }

        $rate = (float) affiliation_setting('click_remuneration_rate', 0);
        if ($rate <= 0) {
            return;
        }

        $autoApprove = affiliation_setting('auto_approve_commissions', '0') === '1';
        $status = $autoApprove ? 'approved' : 'pending';

        DB::transaction(function () use ($affiliate, $rate, $status, $autoApprove) {
            AffiliateCommission::create([
                'affiliate_id'    => $affiliate->id,
                'referral_id'     => null,
                'invoice_id'      => null,
                'amount'          => $rate,
                'commission_rate' => 0,
                'type'            => 'click',
                'description'     => __('Affilix::affiliation.commission_click_description'),
                'status'          => $status,
            ]);

            $affiliate->increment('total_earnings', $rate);

            if ($autoApprove) {
                $affiliate->increment('pending_earnings', $rate);
            }
        });
    }
}
