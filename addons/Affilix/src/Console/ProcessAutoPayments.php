<?php

namespace App\Addons\Affiliation\Console;

use App\Addons\Affiliation\Models\Affiliate;
use App\Addons\Affiliation\Models\AffiliateWithdrawal;
use App\Addons\Affiliation\Models\AffiliationSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessAutoPayments extends Command
{
    protected $signature   = 'affilix:auto-pay';
    protected $description = 'Process automatic affiliate balance payments';

    public function handle(): void
    {
        if (AffiliationSetting::get('auto_payment_enabled', '0') !== '1') {
            $this->info('Auto-pay disabled.');
            return;
        }

        $frequency = AffiliationSetting::get('auto_payment_frequency', 'monthly');
        $threshold = max(0.01, (float) AffiliationSetting::get('auto_payment_threshold', 1));

        if ($frequency === 'monthly' && now()->day !== 1) {
            $this->info('Monthly auto-pay: skipped (not the 1st).');
            return;
        }

        $affiliates = Affiliate::where('status', 'active')
            ->where('payment_method', 'balance')
            ->whereRaw('pending_earnings >= ?', [$threshold])
            ->whereDoesntHave('withdrawals', fn ($q) => $q->where('status', 'pending'))
            ->get();

        $count  = 0;
        $errors = 0;
        foreach ($affiliates as $affiliate) {
            try {
                $withdrawal = AffiliateWithdrawal::create([
                    'affiliate_id'    => $affiliate->id,
                    'amount'          => $affiliate->pending_earnings,
                    'payment_method'  => 'balance',
                    'payment_details' => $affiliate->payment_details,
                    'status'          => 'pending',
                ]);
                $withdrawal->pay('auto-pay-' . now()->format('Y-m-d'));
                $count++;
                $this->line("  → Affilié #{$affiliate->id} : {$affiliate->pending_earnings} payé");
            } catch (\Throwable $e) {
                $errors++;
                $this->error("  ✗ Affilié #{$affiliate->id} : " . $e->getMessage());
                Log::error('Affilix auto-pay failed for affiliate #' . $affiliate->id, [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Auto-pay terminé : {$count} affilié(s) traité(s)" . ($errors ? ", {$errors} erreur(s)." : '.'));
    }
}
