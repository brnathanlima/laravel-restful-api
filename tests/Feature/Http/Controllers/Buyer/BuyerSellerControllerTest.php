<?php

namespace Tests\Feature\Http\Controllers\Buyer;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
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
}
