<?php

use App\Models\User;
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
        Schema::create('jurnals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('jurnal_code')->index()->unique();
            $table->integer('income')->default(0);
            $table->integer('expense')->default(0);
            $table->string('description');
            $table->date('transaction_date');
            $table->uuid('transactable_id');
            $table->string('transactable_type');
            $table->foreignUuid('user_id')->nullable();
            $table->foreign('user_id')->on('users')->references('id')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnals');
    }
};
