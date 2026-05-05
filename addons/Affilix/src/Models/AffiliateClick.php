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
        'is_unique',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'is_unique'  => 'boolean',
    ];

    /**
     * Relation avec l'affilié
     */
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Enregistrer un clic (is_unique = false si la même IP a déjà cliqué pour cet affilié)
     */
    public static function trackClick(Affiliate $affiliate, array $data = []): self
    {
        $ip = request()->ip();

        $isUnique = !self::where('affiliate_id', $affiliate->id)
            ->where('ip_address', $ip)
            ->exists();

        $click = self::create([
            'affiliate_id' => $affiliate->id,
            'referral_code' => $affiliate->referral_code,
            'ip_address' => $ip,
            'user_agent' => request()->userAgent(),
            'referer' => request()->headers->get('referer'),
            'landing_page' => request()->fullUrl(),
            'clicked_at' => now(),
            'is_unique' => $isUnique,
            ...$data,
        ]);

        if ($isUnique) {
            $affiliate->increment('unique_clicks');
        }

        return $click;
    }
}
