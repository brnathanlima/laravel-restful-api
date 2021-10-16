<?php

namespace Tests\Feature\Http\Controllers\Buyer;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BuyerProductControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function testBuyerIsAbleToSeeOwnProducts()
    {
        $buyer = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Transaction::factory()->create([
            'quantity' => 1,
            'product_id' => 1,
            'buyer_id' => $buyer->id
        ]);

        Passport::actingAs($buyer, ['read-general']);

        $this->json('GET', "/buyers/{$buyer->id}/products")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'identifier',
                        'title',
                        'details',
                        'stock',
                        'creationDate',
                        'lastChange',
                        'deletedDate',
                        'links' => [
                            '*' => [
                                'rel',
                                'href'
                            ]
                        ]
                    ]
                ]
            ]);
    }

    public function testBuyerIsNotAbleToSeeOwnProductsWithoutReadGeneralScope()
    {
        $buyer = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Transaction::factory()->create([
            'quantity' => 1,
            'product_id' => 1,
            'buyer_id' => $buyer->id
        ]);

        Passport::actingAs($buyer);

        $this->json('GET', "/buyers/{$buyer->id}/products")
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'Invalid scope(s) provided.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }
}
