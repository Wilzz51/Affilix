<?php

namespace App\Addons\Affiliation\Models;

use App\Addons\Affiliation\Mail\CommissionApproved;
use App\Addons\Affiliation\Mail\CommissionPaid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Billing\Invoice;
use App\Models\Account\Customer;

class AffiliateCommission extends Model
{
    protected $fillable = [
        'affiliate_id',
        'referral_id',
        'invoice_id',
        'amount',
        'commission_rate',
        'description',
        'status',
        'approved_at',
        'paid_at',
        'payment_reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Relation avec l'affilié
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Relation avec le parrainage
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    /**
     * Relation avec la facture
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Approuver la commission
     */
    public function approve(): void
    {
        DB::transaction(function () {
            $this->update([
                'status'      => 'approved',
                'approved_at' => now(),
            ]);

            $this->affiliate->increment('pending_earnings', $this->amount);
        });

        try {
            Mail::to($this->affiliate->customer->email)->send(new CommissionApproved($this));
        } catch (\Throwable $e) {
            Log::warning('Affilix: failed to send CommissionApproved email', [
                'commission_id' => $this->id,
                'error'         => $e->getMessage(),
            ]);
        }
    }

    /**
     * Marquer comme payée
     */
    public function markAsPaid(string $reference = null): void
    {
        DB::transaction(function () use ($reference) {
            $this->update([
                'status'            => 'paid',
                'paid_at'           => now(),
                'payment_reference' => $reference,
            ]);

            $this->affiliate->decrement('pending_earnings', $this->amount);
            $this->affiliate->increment('paid_earnings', $this->amount);

            if ($this->affiliate->payment_method === 'balance') {
                $customer = Customer::find($this->affiliate->customer_id);
                if ($customer) {
                    $customer->addFund(
                        (float) $this->amount,
                        'Commission affiliation #' . $this->id . ($reference ? ' - ' . $reference : '')
                    );
                }
            }
        });

        try {
            Mail::to($this->affiliate->customer->email)->send(new CommissionPaid($this));
        } catch (\Throwable $e) {
            Log::warning('Affilix: failed to send CommissionPaid email', [
                'commission_id' => $this->id,
                'error'         => $e->getMessage(),
            ]);
        }
    }

    public function cancel(string $reason = null): void
    {
        if ($this->status === 'cancelled') {
            return;
        }

        DB::transaction(function () use ($reason) {
            if ($this->status === 'approved') {
                $this->affiliate->decrement('pending_earnings', $this->amount);
            } elseif ($this->status === 'paid') {
                $this->affiliate->decrement('paid_earnings', $this->amount);
            }

            $this->affiliate->decrement('total_earnings', $this->amount);

            $this->update([
                'status' => 'cancelled',
                'notes'  => $reason,
            ]);
        });
    }
}
