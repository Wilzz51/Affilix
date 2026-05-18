<?php

namespace App\Addons\Affiliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class AffiliateWithdrawal extends Model
{
    protected $fillable = [
        'affiliate_id',
        'amount',
        'payment_method',
        'payment_details',
        'status',
        'payment_reference',
        'admin_note',
        'paid_at',
    ];

    protected $casts = [
        'amount'          => 'decimal:2',
        'payment_details' => 'array',
        'paid_at'         => 'datetime',
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function pay(?string $reference = null): void
    {
        DB::transaction(function () use ($reference) {
            $this->update([
                'status'            => 'paid',
                'payment_reference' => $reference,
                'paid_at'           => now(),
            ]);

            // Mark all currently approved commissions as paid (suppress per-commission emails)
            $commissions = $this->affiliate->commissions()
                ->where('status', 'approved')
                ->orderBy('created_at')
                ->get();

            foreach ($commissions as $commission) {
                $commission->markAsPaid($reference, false);
            }
        });
    }

    public function reject(?string $note = null): void
    {
        $this->update([
            'status'     => 'rejected',
            'admin_note' => $note,
        ]);
    }
}
