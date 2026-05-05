<?php

namespace App\Addons\Affiliation\Mail;

use App\Addons\Affiliation\Models\AffiliateCommission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommissionApproved extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AffiliateCommission $commission) {}

    public function build(): static
    {
        return $this
            ->subject(__('Affilix::affiliation.emails.commission_approved_subject'))
            ->view('affilix_mail::commission_approved');
    }
}
