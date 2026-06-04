<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->decimal('after_first_order_commission_rate', 10, 4)->nullable()->after('first_order_commission_type');
            $table->enum('after_first_order_commission_type', ['percent', 'fixed'])->nullable()->after('after_first_order_commission_rate');
        });
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn(['after_first_order_commission_rate', 'after_first_order_commission_type']);
        });
    }
};
