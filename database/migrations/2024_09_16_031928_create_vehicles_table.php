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
            $table->uuid('id')->primar();
            $table->string('plate_number', 20);
            $table->string('machine_frmae', 50);
            $table->string('engine_volume', 10);
            $table->enum('engine_type', ['petrol', 'diesel']);
            $table->enum('type', ['car', 'motorcycle']);
            $table->year('production_year');
            $table->foreignIdFor(Brand::class)->nullable()->constrained()->nullOnDelete();
            $table->foreignIdFor(Customer::class)->constrained()->restrictOnDelete();
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
