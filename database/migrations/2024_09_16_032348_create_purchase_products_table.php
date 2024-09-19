<?php

use App\Models\Product;
use App\Models\Purchase;
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
        Schema::create('purchase_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id');
            $table->foreignUuid('purchase_id');
            $table->foreign('product_id')->on('products')->references('id')->onDelete('restrict');
            $table->foreign('purchase_id')->on('purchases')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('purchase_products');
    }
};
