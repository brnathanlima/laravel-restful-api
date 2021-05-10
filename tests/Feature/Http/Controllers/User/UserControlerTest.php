<?php

namespace Tests\Feature\Htpp\Controllers\User;

use Illuminate\Http\Response;
use \App\Models\User;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UserControlerTest extends TestCase
{
    protected $scopes = [];

    public function testAdminIsAbleToListUsers()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => true
        ]);

        Passport::actingAs($user, ['read-general']);

        $this->json('GET', '/users')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'identifier',
                        'name',
                        'email',
                        'isVerified',
                        'isAdmin',
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

    public function testAdminIsNotAbleToListUsersWithoutReadGeneralScope()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => true
        ]);

        Passport::actingAs($user, ['manage-products']);

        $this->json('GET', '/users')
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'Invalid scope(s) provided.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testRegularUserIsNotAbleToListUsers()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, ['read-general']);

        $this->json('GET', '/users')
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'This action is unauthorized',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testClientIsAbleToCreateUser()
    {
        Passport::actingAsClient(Client::factory()->create(), []);

        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'passwordConfirmation' => 'password'
        ];

        $this->json('POST', '/users', $payload, ['Accept' => 'application/json'])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'identifier',
                    'name',
                    'email',
                    'isVerified',
                    'isAdmin',
                    'isAdmin',
                    'lastChange',
                    'deletedDate',
                    'links' => [
                        '*' => [
                            'rel',
                            'href'
                        ]
                    ]
                ]
            ]);
    }

    public function testClientIsNotAbleToCreateUserWithMissingData()
    {
        Passport::actingAsClient(Client::factory()->create(), []);

        $this->json('POST', '/users')
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertExactJson([
                'error' => [
                    'name' => [
                        'The name field is required.'
                    ],
                    'email' => [
                        'The email field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ]
                ],
                'code' => 422
            ]);
    }

    public function testUserIsAbleToUpdateOwnData()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, ['manage-account']);

        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
        ];

        $this->json('PUT', "users/$user->id", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'data' => [
                    'identifier' => $user->id,
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'isVerified' => 0,
                    'isAdmin' => false,
                    'registeredAt' => $user->created_at,
                    'lastChange' => $user->updated_at,
                    'deletedDate' => $user->deleted_at,
                    'links' => [
                        [
                            'rel' => 'self',
                            'href' => route('users.show', $user->id)
                        ]
                    ]
                ]
            ]);
    }

    public function testUserIsNotAbleToUpdateAdminField()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, ['manage-account']);

        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'isAdmin' => true
        ];

        $this->json('PUT', "users/$user->id", $payload)
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'This action is unauthorized',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testUnverifiedUserIsNotAbleToUpdateAdminField()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => true
        ]);

        Passport::actingAs($user, ['manage-account']);

        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'isAdmin' => true
        ];

        $this->json('PUT', "users/$user->id", $payload)
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertExactJson([
                'error' => 'Only verified users can modify the admin field.',
                'code' => Response::HTTP_CONFLICT
            ]);
    }

    public function testUserIsAbleToUpdateAdminField()
    {
        $user = User::factory()->create([
            'verified' => 1,
            'verification_token' => User::generateVerificationCode(),
            'admin' => true
        ]);

        Passport::actingAs($user, ['manage-account']);

        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => 'password',
            'isAdmin' => false
        ];

        $this->json('PUT', "users/$user->id", $payload)
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'data' => [
                    'identifier' => $user->id,
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'isVerified' => 0,
                    'isAdmin' => false,
                    'registeredAt' => $user->created_at,
                    'lastChange' => $user->updated_at,
                    'deletedDate' => $user->deleted_at,
                    'links' => [
                        [
                            'rel' => 'self',
                            'href' => route('users.show', $user->id)
                        ]
                    ]
                ]
            ]);
    }

    public function testShowSpecificUser()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, ['manage-account']);

        $this->json('GET', "/users/{$user->id}")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'data' => [
                    'identifier' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'isVerified' => $user->verified,
                    'isAdmin' => $user->admin,
                    'registeredAt' => $user->created_at,
                    'lastChange' => $user->updated_at,
                    'deletedDate' => $user->deleted_at,
                    'links' => [
                        [
                            'rel' => 'self',
                            'href' => route('users.show', $user->id)
                        ]
                    ]
                ]
            ]);
    }

    public function testUserIsAbleToSeeOwnData()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, ['manage-account']);

        $this->json('GET', '/users/me')
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'data' => [
                    'identifier' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'isVerified' => $user->verified,
                    'isAdmin' => $user->admin,
                    'registeredAt' => $user->created_at,
                    'lastChange' => $user->updated_at,
                    'deletedDate' => $user->deleted_at,
                    'links' => [
                        [
                            'rel' => 'self',
                            'href' => route('users.show', $user->id)
                        ]
                    ]
                ]
            ]);
    }

    public function testUserIsNotAbleToSeeOwnData()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, []);

        $this->json('GET', '/users/me')
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'Invalid scope(s) provided.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testUserIsAbleToSoftDeleteOwnAccount()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        Passport::actingAs($user, []);

        $this->json('DELETE', "/users/{$user->id}")
            ->assertNoContent();

        $this->assertSoftDeleted($user);
    }

    public function testUserIsNotAbleToSoftDeleteAnotherAccount()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        $user2 = User::factory()->create([
            'verification_token' => User::generateVerificationCode(),
        ]);

        Passport::actingAs($user, []);

        $this->json('DELETE', "/users/{$user2->id}")
            ->assertForbidden()
            ->assertExactJson([
                'error' => 'This action is unauthorized.',
                'code' => Response::HTTP_FORBIDDEN
            ]);
    }

    public function testUserVerification()
    {
        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        $this->json('GET', "/users/verify/{$user->verification_token}")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'message' => 'The account has been successfully verified',
                'code' => Response::HTTP_OK
            ]);
    }

    public function testResendVerificationToken()
    {
        Passport::actingAsClient(Client::factory()->create(), []);

        $user = User::factory()->create([
            'verified' => 0,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        $this->json('GET', "/users/{$user->id}/resend")
            ->assertStatus(Response::HTTP_OK)
            ->assertExactJson([
                'message' => 'The verification token has been resend',
                'code' => Response::HTTP_OK
            ]);
    }

    public function testResendVerificationTokenToAlreadyVerifiedUser()
    {
        Passport::actingAsClient(Client::factory()->create(), []);

        $user = User::factory()->create([
            'verified' => 1,
            'verification_token' => User::generateVerificationCode(),
            'admin' => false
        ]);

        $this->json('GET', "/users/{$user->id}/resend")
            ->assertStatus(Response::HTTP_CONFLICT)
            ->assertExactJson([
                'error' => 'This user is already verified',
                'code' => Response::HTTP_CONFLICT
            ]);
    }
}
