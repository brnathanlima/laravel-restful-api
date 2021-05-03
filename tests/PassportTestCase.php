<?php

namespace Tests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\ClientRepository;

abstract class PassportTestCase extends TestCase
{
    use DatabaseTransactions;

    protected $headers;
    protected $scopes;
    protected $user;

    public function setUp() : void
    {
        parent::setUp();

        $clientRepository = new ClientRepository();
        $client = $clientRepository->createPersonalAccessClient(
            null,
            'Test Personal Access Client',
            '/'
        );

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => $client->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $this->user = User::create([
            'name' => 'Test user',
            'email' => 'test@doma.in',
            'password' => bcrypt('password'),
            'verified' => 1,
            'verification_token' => User::generateVerificationCode(),
            'admin' => 'true'
        ]);
        $token = $this->user->createToken('testToken', $this->scopes)->accessToken;
        $this->headers['Authorization'] = "Bearer {$token}";
    }

    public function json($method, $uri, array $data = [], array $headers = [])
    {
        return parent::json($method, $uri, $data, array_merge($this->headers, $headers));
    }
}
