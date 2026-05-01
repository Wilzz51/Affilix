<?php

namespace App\Addons\Affiliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateClick extends Model
{
    protected $fillable = [
        'affiliate_id',
        'referral_code',
        'ip_address',
        'user_agent',
        'referer',
        'landing_page',
        'clicked_at',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    /**
     * Relation avec l'affilié
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Enregistrer un clic
     */
    public static function trackClick(Affiliate $affiliate, array $data = []): self
    {
        return self::create([
            'affiliate_id' => $affiliate->id,
            'referral_code' => $affiliate->referral_code,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->headers->get('referer'),
            'landing_page' => request()->fullUrl(),
            'clicked_at' => now(),
            ...$data,
        ]);
    }
}
