<?php
namespace Tests\Feature;

use App\Enums\TransactionStatusEnum;
use App\Models\User;
use App\Models\UserWallet;
use App\Repositories\UserTransactionRepository;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use DatabaseTransactions;
    public function test_transaction_fails_when_sender_has_insufficient_balance(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this->actingAs($userA)->post('api/transaction', [
            'amount'    => 100,
            'recipient' => $userB->email,
            'type'      => 'transfer',
        ]);

        $response->assertStatus(400);
    }

    public function test_transaction_fails_when_sender_is_recipient(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('api/transaction', [
            'amount'    => 100,
            'recipient' => $user->email,
            'type'      => 'transfer',
        ]);

        $response->assertStatus(400);
    }

    public function test_transaction_succeeds_when_sender_has_sufficient_balance(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        UserWallet::factory()->create([
            'user_id' => $userA->id,
            'balance' => 1000,
        ]);

        $response = $this->actingAs($userA)->post('api/transaction', [
            'amount'    => 100,
            'recipient' => $userB->email,
            'type'      => 'transfer',
        ]);

        $response->assertStatus(200);
    }

    public function test_transaction_is_processed_correctly(): void
    {
        $walletServ = new WalletService();

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transferAmount = 100;

        UserWallet::factory()->create([
            'user_id' => $userA->id,
            'balance' => 1000,
        ]);

        $transaction = $walletServ->sendTransfer($userA, $userB, $transferAmount);

        
        $transaction = $walletServ->completeTransaction($transaction);
        
        $userA = $userA->fresh();
        $userB = $userB->fresh();

        $this->assertEquals($userA->wallet->balance, 900);
        $this->assertEquals($userB->wallet->balance, 100);        
    }

    public function test_transaction_is_cancelled_correctly(): void
    {
        $walletServ = new WalletService();
        $userTransactionRep = new UserTransactionRepository();

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transferAmount = 100;

        UserWallet::factory()->create([
            'user_id' => $userA->id,
            'balance' => 1000,
        ]);

        $transaction = $walletServ->sendTransfer($userA, $userB, $transferAmount);

        
        $transaction = $walletServ->cancelTransaction($transaction);
        
        $userA = $userA->fresh();
        $transaction = $transaction->fresh();

        $this->assertEquals($userA->wallet->balance, 1000);
        $this->assertEquals($userTransactionRep->getCurrentStatus($transaction)->name, 'cancelled');
    }

    public function test_completed_transaction_refund_is_processed_correctly(): void
    {
        $walletServ = new WalletService();

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transferAmount = 100;

        UserWallet::factory()->create([
            'user_id' => $userA->id,
            'balance' => 1000,
        ]);

        $transaction = $walletServ->sendTransfer($userA, $userB, $transferAmount);

        $walletServ->completeTransaction($transaction);
        
        $userA = $userA->fresh();
        $userB = $userB->fresh();

        $transaction = $walletServ->completeRefund($transaction->fresh());

        $userA = $userA->fresh();
        $userB = $userB->fresh();

        $this->assertEquals($userA->wallet->balance, 1000);
        $this->assertEquals($userB->wallet->balance, 0);
    }
}
