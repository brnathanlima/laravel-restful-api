<?php

namespace Tests\Feature\User;

use Illuminate\Http\Response;
use Tests\PassportTestCase;

class UserControlerTest extends PassportTestCase
{
    protected $scopes = ['read-general'];

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testListingUsers()
    {
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
}
