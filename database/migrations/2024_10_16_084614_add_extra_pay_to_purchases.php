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
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('invoice_number')->nullable()->after('id');
            $table->bigInteger('extra_pay')->default(0)->after('payment_id');
            $table->date('transaction_date')->nullable()->after('total');
            $table->enum('status', ['create', 'finish'])->default('create')->after('transaction_date');
        });
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->bigInteger('old_price')->default(0)->after('qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('extra_pay');
            $table->dropColumn('transaction_date');
            $table->dropColumn('invoice_number');
            $table->dropColumn('status');
        });
        Schema::table('purchase_products', function (Blueprint $table) {
            $table->dropColumn('old_price');
        });
    }
};
