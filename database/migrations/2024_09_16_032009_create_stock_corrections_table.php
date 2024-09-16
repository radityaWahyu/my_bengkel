<?php

use App\Models\Product;
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
        Schema::create('stock_corrections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnDelete();
            $table->integer('old_stock');
            $table->integer('new_stock');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_corrections');
    }
};
