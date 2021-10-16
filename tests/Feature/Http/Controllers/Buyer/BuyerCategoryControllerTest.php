<?php

namespace Tests\Feature\Http\Controllers\Buyer;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BuyerCategoryControllerTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        $this->seed();
    }

    public function testBuyerIsAbleToSeeOwnCategories()
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

        $this->json('GET', "/buyers/{$buyer->id}/categories")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        "identifier",
                        "title",
                        "details",
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

    public function testBuyerIsNotAbleToSeeOwnCategoriesWithoutReadGeneralScope()
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

        $this->json('GET', "/buyers/{$buyer->id}/categories")
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'Invalid scope(s) provided.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testBuyerIsNotAbleToSeeAnotherBuyerCategories()
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

        $this->json('GET', "/buyers/{$buyers[1]->id}/categories")
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'This action is unauthorized.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }
}
