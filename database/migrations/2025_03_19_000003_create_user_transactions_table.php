<?php

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
        Schema::create('user_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('target_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('currency_id')->references('id')->on('currencies');
            $table->integer('amount');
            $table->integer('total_amount')->nullable();
            $table->string('type', 20)->default('deposit');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
            $table->index(['user_id', 'target_user_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_transactions');
    }
};
