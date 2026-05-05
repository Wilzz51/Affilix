<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Clics uniques : flag + index composite sur affiliate_clicks
        Schema::table('affiliate_clicks', function (Blueprint $table) {
            $table->boolean('is_unique')->default(true)->after('clicked_at');
            $table->index(['affiliate_id', 'ip_address'], 'affiliate_clicks_affiliate_ip_index');
        });

        // 2. Compteur de clics uniques sur l'affilié
        Schema::table('affiliates', function (Blueprint $table) {
            $table->unsignedBigInteger('unique_clicks')->default(0)->after('total_referrals');
        });

        // 3. Type de commission (sale | click)
        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->string('type')->default('sale')->after('status');
        });

        $driver = DB::getDriverName();

        // 4. Rendre referral_id nullable (sans doctrine/dbal)
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE affiliate_commissions DROP FOREIGN KEY affiliate_commissions_referral_id_foreign');
            DB::statement('ALTER TABLE affiliate_commissions MODIFY COLUMN referral_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE affiliate_commissions ADD CONSTRAINT affiliate_commissions_referral_id_foreign FOREIGN KEY (referral_id) REFERENCES referrals(id) ON DELETE SET NULL');

            // 5a. Backfill is_unique : premier clic par (affiliate_id, ip_address) = unique
            DB::statement("
                UPDATE affiliate_clicks ac
                JOIN (
                    SELECT MIN(id) AS min_id, affiliate_id, ip_address
                    FROM affiliate_clicks
                    GROUP BY affiliate_id, ip_address
                ) AS first_clicks
                ON ac.affiliate_id = first_clicks.affiliate_id
                AND ac.ip_address = first_clicks.ip_address
                SET ac.is_unique = (ac.id = first_clicks.min_id)
            ");
        }

        // 5b. Backfill unique_clicks sur les affiliés existants (compatible tous drivers)
        $affiliateIds = DB::table('affiliates')->pluck('id');
        foreach ($affiliateIds as $id) {
            $uniqueCount = DB::table('affiliate_clicks')
                ->where('affiliate_id', $id)
                ->where('is_unique', true)
                ->count();
            DB::table('affiliates')->where('id', $id)->update(['unique_clicks' => $uniqueCount]);
        }
    }

    public function down(): void
    {
        Schema::table('affiliate_clicks', function (Blueprint $table) {
            $table->dropIndex('affiliate_clicks_affiliate_ip_index');
            $table->dropColumn('is_unique');
        });

        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn('unique_clicks');
        });

        Schema::table('affiliate_commissions', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        // Note : referral_id reste nullable — le remettre NOT NULL échouerait
        // si des commissions de type 'click' existent déjà
    }
};
