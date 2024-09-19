<?php

use App\Models\Product;
use App\Models\Sale;
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
        Schema::create('sale_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id');
            $table->foreignUuid('sale_id');
            $table->foreign('product_id')->on('products')->references('id')->onDelete('restrict');
            $table->foreign('sale_id')->on('sales')->references('id')->onDelete('cascade');
            $table->integer('qty');
            $table->bigInteger('price');
            $table->bigInteger('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_products');
    }
};
