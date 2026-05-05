<?php

namespace App\Addons\Affiliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Account\Customer;

class Affiliate extends Model
{
    protected $fillable = [
        'customer_id',
        'referral_code',
        'commission_rate',
        'total_earnings',
        'pending_earnings',
        'paid_earnings',
        'total_referrals',
        'unique_clicks',
        'successful_referrals',
        'status',
        'payment_method',
        'payment_details',
        'approved_at',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_earnings' => 'decimal:2',
        'pending_earnings' => 'decimal:2',
        'paid_earnings' => 'decimal:2',
        'payment_details' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Relation avec le client
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Relation avec les parrainages
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Relation avec les commissions
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    /**
     * Relation avec les clics
     */
    public function clicks(): HasMany
    {
        return $this->hasMany(AffiliateClick::class);
    }

    /**
     * Générer un code de parrainage unique
     */
    public static function generateReferralCode(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $code = Str::upper(Str::random(8));
            if (!self::where('referral_code', $code)->exists()) {
                return $code;
            }
        }

        // Fallback avec timestamp pour garantir l'unicité
        return Str::upper(Str::random(6)) . strtoupper(base_convert((string) time(), 10, 36));
    }

    /**
     * Vérifier si l'affilié est actif
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Obtenir le taux de conversion
     */
    public function getConversionRate(): float
    {
        if ($this->total_referrals === 0) {
            return 0;
        }

        return ($this->successful_referrals / $this->total_referrals) * 100;
    }

    public function getReferralUrl(): string
    {
        return route('affiliation.track', ['code' => $this->referral_code]);
    }

    public function recalculateStats(): void
    {
        $this->update([
            'total_earnings'       => $this->commissions()->whereNotIn('status', ['cancelled'])->sum('amount'),
            'pending_earnings'     => $this->commissions()->where('status', 'approved')->sum('amount'),
            'paid_earnings'        => $this->commissions()->where('status', 'paid')->sum('amount'),
            'total_referrals'      => $this->referrals()->count(),
            'successful_referrals' => $this->referrals()->where('status', 'converted')->count(),
            'unique_clicks'        => $this->clicks()->where('is_unique', true)->count(),
        ]);
    }
}
