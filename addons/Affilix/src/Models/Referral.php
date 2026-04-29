<?php

namespace App\Addons\Affiliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Account\Customer;

class Referral extends Model
{
    protected $fillable = [
        'affiliate_id',
        'customer_id',
        'referral_code',
        'ip_address',
        'user_agent',
        'clicked_at',
        'registered_at',
        'first_purchase_at',
        'status',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'registered_at' => 'datetime',
        'first_purchase_at' => 'datetime',
    ];

    /**
     * Relation avec l'affilié
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Relation avec le client
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relation avec les commissions
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    /**
     * Marquer comme inscrit
     */
    public function markAsRegistered(): void
    {
        $this->update([
            'registered_at' => now(),
            'status' => 'registered',
        ]);

        $this->affiliate->increment('total_referrals');
    }

    /**
     * Marquer comme converti
     */
    public function markAsConverted(): void
    {
        $this->update([
            'first_purchase_at' => now(),
            'status' => 'converted',
        ]);

        $this->affiliate->increment('successful_referrals');
    }
}
