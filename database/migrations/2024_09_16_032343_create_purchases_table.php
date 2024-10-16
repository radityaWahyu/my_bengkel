<?php

use App\Models\User;
use App\Models\Payment;
use App\Models\Supplier;
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
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('purchase_code', 50);
            $table->foreignUuid('user_id');
            $table->foreignUuid('supplier_id')->nullable();
            $table->foreignUuid('payment_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('supplier_id')->on('suppliers')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('purchases');
    }
};
