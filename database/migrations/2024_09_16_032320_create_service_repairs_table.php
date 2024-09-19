<?php

use App\Models\Employee;
use App\Models\Repair;
use App\Models\Service;
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
        Schema::create('service_repairs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('repair_id');
            $table->foreignUuid('service_id');
            $table->foreignUuid('employee_id')->nullable();
            $table->foreign('repair_id')->on('repairs')->references('id')->onDelete('restrict');
            $table->foreign('service_id')->on('services')->references('id')->onDelete('cascade');
            $table->foreign('employee_id')->on('employees')->references('id')->onDelete('set null');
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
        Schema::dropIfExists('service_repairs');
    }
};
