<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('affiliation_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insérer les paramètres par défaut
        $defaultSettings = [
            ['key' => 'default_commission_rate', 'value' => '10'],
            ['key' => 'auto_approve', 'value' => '1'],
            ['key' => 'auto_approve_commissions', 'value' => '0'],
            ['key' => 'minimum_payout', 'value' => '50'],
            ['key' => 'cookie_lifetime', 'value' => '30'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('affiliation_settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliation_settings');
    }
};
