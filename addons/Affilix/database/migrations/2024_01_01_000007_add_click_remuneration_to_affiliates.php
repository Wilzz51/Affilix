<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->boolean('click_remuneration_enabled')->nullable()->default(null)->after('commission_rate');
            $table->decimal('click_remuneration_rate', 10, 4)->nullable()->default(null)->after('click_remuneration_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn(['click_remuneration_enabled', 'click_remuneration_rate']);
        });
    }
};
