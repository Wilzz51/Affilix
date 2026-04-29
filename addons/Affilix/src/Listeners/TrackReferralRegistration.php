<?php

namespace App\Addons\Affiliation\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Addons\Affiliation\Models\Affiliate;
use App\Addons\Affiliation\Models\Referral;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class TrackReferralRegistration
{
    public function handle(Registered $event): void
    {
        $customer = $event->user;
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

        $existingReferral = Referral::where('customer_id', $customer->id)
            ->where('affiliate_id', $affiliate->id)
            ->first();

        if ($existingReferral) {
            $existingReferral->markAsRegistered();
        } else {
            Referral::create([
                'affiliate_id'  => $affiliate->id,
                'customer_id'   => $customer->id,
                'referral_code' => $referralCode,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent(),
                'clicked_at'    => now(),
                'registered_at' => now(),
                'status'        => 'registered',
            ]);

            $affiliate->increment('total_referrals');
        }

        Session::forget('referral_code');
        Cookie::queue(Cookie::forget('referral_code'));
    }
}
