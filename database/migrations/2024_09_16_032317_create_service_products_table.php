<?php

use App\Models\Product;
use App\Models\Service;
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
        Schema::create('service_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id');
            $table->foreignUuid('service_id');
            $table->foreign('product_id')->on('products')->references('id')->onDelete('restrict');
            $table->foreign('service_id')->on('services')->references('id')->onDelete('cascade');
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
        Schema::dropIfExists('service_products');
    }
};
