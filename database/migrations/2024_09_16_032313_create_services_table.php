<?php

use App\Models\Payment;
use App\Models\User;
use App\Models\Vehicle;
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
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('service_code', 50);
            $table->foreignUuid('user_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id')->onDelete('set null');
            $table->enum('status', ['waiting', 'process', 'pending', 'finish'])->default('waiting');
            $table->bigInteger('total')->default(0);
            $table->foreignUuid('vehicle_id');
            $table->foreign('vehicle_id')->on('vehicles')->references('id')->onDelete('restrict');
            $table->foreignUuid('payment_id')->nullable();
            $table->foreign('payment_id')->on('payments')->references('id')->onDelete('set null');
            $table->text('descrtiption');
            $table->text('notes')->nullable();
            $table->date('finished_date')->nullable();
            $table->date('return_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
