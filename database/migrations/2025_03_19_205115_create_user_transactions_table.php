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
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('wallet_id')->references('id')->on('user_wallets');
            $table->foreignId('target_walled_id')->references('id')->on('user_wallets')->nullable();
            $table->foreignId('status_id')->references('id')->on('transaction_statuses');
            $table->foreignId('currency_id')->references('currencies');
            $table->integer('amount');
            $table->string('type', 20)->default('deposit');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
            $table->index(['user_id', 'wallet_id']);
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
