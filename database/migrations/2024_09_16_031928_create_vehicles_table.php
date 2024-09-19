<?php

use App\Models\Brand;
use App\Models\Customer;
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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('plate_number', 20);
            $table->string('machine_frmae', 50);
            $table->string('engine_volume', 10);
            $table->enum('engine_type', ['petrol', 'diesel']);
            $table->enum('type', ['car', 'motorcycle']);
            $table->year('production_year');
            $table->foreignUuid('brand_id')->nullable();
            $table->foreignUuid('customer_id');
            $table->foreign('brand_id')->on('brands')->references('id')->onDelete('set null');
            $table->foreign('customer_id')->on('customers')->references('id')->onDelete('restrict');
            // $table->foreignIdFor(Brand::class)->nullable()->constrained()->nullOnDelete();
            // $table->foreignIdFor(Customer::class)->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
