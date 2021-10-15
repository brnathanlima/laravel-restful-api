<?php

namespace Tests\Feature\Http\Controllers\Buyer;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class BuyerControllerTest extends TestCase
{
    protected $scopes = [];

    public function setUp() : void
    {
        parent::setUp();

        $this->seed();
    }

    public function testAdminIsAbleToListBuyers()
    {
        $admin = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => true
        ]);

        Passport::actingAs($admin, ['read-general']);

        $this->json('GET', '/buyers')
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

    public function testAdminIsNotAbleToListBuyersWithoutReadGeneralScope()
    {
        $admin = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => true
        ]);

        Passport::actingAs($admin);

        $this->json('GET', '/buyers')
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'Invalid scope(s) provided.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testRegularUserIsNotAbleToListBuyers()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, ['read-general']);

        $this->json('GET', '/buyers')
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'This action is unauthorized',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testBuyerIsAbleToSeeOwnProfile()
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

        $this->json('GET', "/buyers/{$buyer->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'data' => [
                    'identifier' => $buyer->id,
                    'name' => $buyer->name,
                    'email' => $buyer->email,
                    'isVerified' => $buyer->verified,
                    'registeredAt' => $buyer->created_at,
                    'lastChange' => $buyer->updated_at,
                    'deletedDate' => $buyer->deleted_at,
                    'links' => [
                        [
                            'rel' => 'self',
                            'href' => route('buyers.show', $buyer->id)
                        ],
                        [
                            'rel' => 'parent',
                            'href' => route('users.show', $buyer->id)
                        ],
                        [
                            'rel' => 'buyers.categories',
                            'href' => route('buyers.categories', $buyer->id)
                        ],
                        [
                            'rel' => 'buyers.products',
                            'href' => route('buyers.products', $buyer->id)
                        ],
                        [
                            'rel' => 'buyers.sellers',
                            'href' => route('buyers.sellers', $buyer->id)
                        ],
                        [
                            'rel' => 'buyers.transactions',
                            'href' => route('buyers.transactions', $buyer->id)
                        ],
                    ]
                ]
            ]);
    }
}
