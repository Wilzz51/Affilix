<?php

namespace App\Addons\Affiliation\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AffiliationSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Obtenir une valeur de paramètre
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("affiliation_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Définir une valeur de paramètre
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("affiliation_setting_{$key}");
    }

    /**
     * Obtenir tous les paramètres sous forme de tableau
     */
    public static function all_settings(): array
    {
        return Cache::remember('affiliation_all_settings', 3600, function () {
            return self::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Vider le cache des paramètres
     */
    public static function clearCache(): void
    {
        Cache::forget('affiliation_all_settings');
        
        $keys = ['default_commission_rate', 'auto_approve', 'auto_approve_commissions', 'minimum_payout', 'cookie_lifetime'];
        foreach ($keys as $key) {
            Cache::forget("affiliation_setting_{$key}");
        }
    }
}
