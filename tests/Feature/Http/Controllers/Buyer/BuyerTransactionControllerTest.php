<?php

namespace Tests\Feature\Http\Controllers\Buyer;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BuyerTransactionControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testBuyerIsAbleToSeeOwnTransactions()
    {
        $buyer = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Transaction::factory()->create([
            'quantity' => 4,
            'product_id' => 1,
            'buyer_id' => $buyer->id
        ]);

        Passport::actingAs($buyer, ['read-general']);

        $this->json('GET', "/buyers/{$buyer->id}/transactions")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        "identifier",
                        "quantity",
                        "buyer",
                        "product",
                        "creationDate",
                        "lastChange",
                        "deletedDate",
                        "links" => [
                            '*' => [
                                "rel",
                                "href"
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function testBuyerIsNotAbleToSeeOwnTransactionsWithoutReadGeneralScope()
    {
        $buyer = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Transaction::factory()->create([
            'quantity' => 4,
            'product_id' => 1,
            'buyer_id' => $buyer->id
        ]);

        Passport::actingAs($buyer);

        $this->json('GET', "/buyers/{$buyer->id}/transactions")
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'Invalid scope(s) provided.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testBuyerIsNotAbleToSeeAnotherBuyerTransactions()
    {
        $buyers = User::factory()->count(2)->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        foreach ($buyers as $buyer) {
            Transaction::factory()->create([
                'quantity' => 4,
                'product_id' => 1,
                'buyer_id' => $buyer->id
            ]);
        }

        Passport::actingAs($buyers[0], ['read-general']);

        $this->json('GET', "/buyers/{$buyers[1]->id}/transactions")
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'This action is unauthorized.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }
}
