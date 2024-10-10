<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->bigInteger('extra_pay')->default(0)->nullable()->after('payment_id');
            $table->bigInteger('paid')->default(0)->nullable()->after('total');
            $table->enum('status', ['create', 'finish'])->nullable()->default('create')->after('paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('extra_pay');
            $table->dropColumn('paid');
            $table->dropColumn('status');
        });
    }
};
