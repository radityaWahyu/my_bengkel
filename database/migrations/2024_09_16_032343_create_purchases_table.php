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
            $table->foreignIdFor(User::class)->nullable()->constrained()->nullOnDelete();
            $table->bigInteger('total')->default(0);
            $table->foreignIdFor(Supplier::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Payment::class)->nullable()->constrained()->nullOnDelete();
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
