<?php

namespace Tests\Feature\Http\Controllers\Buyer;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BuyerSellerControllerTest extends TestCase
{
    public function setUp() : void
    {
        parent::setUp();

        $this->seed();
    }

    public function testAdminIsAbleToListBuyerSellers()
    {
        $admin = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => true
        ]);

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

        Passport::actingAs($admin, ['read-general']);

        $this->json('GET', "/buyers/{$buyer->id}/sellers")
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'identifier',
                        'name',
                        'email',
                        'isVerified',
                        'registeredAt',
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

    public function testRegularUserIsNotAbleToListBuyerSellers()
    {
        $regularUser = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

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

        Passport::actingAs($regularUser, ['read-general']);

        $this->json('GET', "/buyers/{$buyer->id}/sellers")
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'This action is unauthorized',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }
}
