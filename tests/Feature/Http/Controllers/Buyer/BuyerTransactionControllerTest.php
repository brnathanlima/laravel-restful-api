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
}
