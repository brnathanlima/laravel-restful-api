<?php

namespace Tests\Feature\Http\Controllers\Buyer;

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
}
