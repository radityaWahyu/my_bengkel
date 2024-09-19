<?php

use App\Models\Rack;
use App\Models\User;
use App\Models\Category;
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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->integer('stock')->default(0);
            $table->bigInteger('sale_price')->default(0);
            $table->bigInteger('buy_price')->default(0);
            $table->foreignUuid('category_id')->nullable();
            $table->foreignUuid('user_id')->nullable();
            $table->foreignUuid('rack_id')->nullable();
            $table->foreign('category_id')->on('categories')->references('id')->onDelete('set null');
            $table->foreign('user_id')->on('users')->references('id')->onDelete('set null');
            $table->foreign('rack_id')->on('racks')->references('id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
