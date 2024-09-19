<?php

use App\Models\User;
use App\Models\Payment;
use App\Models\Customer;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sale_code', 50);
            $table->foreignUuid('user_id')->nullable();
            $table->foreignUuid('customer_id');
            $table->foreignUuid('payment_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('customer_id')->on('customers')->references('id')->onDelete('restrict');
            $table->foreign('payment_id')->on('payments')->references('id')->onDelete('set null');
            $table->bigInteger('total')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
