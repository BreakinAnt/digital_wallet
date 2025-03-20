<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserAuthenticationTest extends TestCase
{
    use DatabaseTransactions;
    public function test_user_can_register(): void
    {
        $response = $this->post('/api/user', [
            'name'                  => 'John Doe',
            'email'                 => 'johndoe@test.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]
        );

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@test.com',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_login_with_verified_email(): void
    {
        $user = User::factory()->create(['password' => '123']);

        $this->post('/api/user/login', [
            'email'    => $user->email,
            'password' => '123',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_unverified_email(): void
    {
        $user = User::factory()->create(['password' => '123', 'email_verified_at' => null]);

        $response = $this->post('/api/user/login', [
            'email'    => $user->email,
            'password' => '123',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_fetch_their_info(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user');

        $response->assertStatus(200);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/logout');

        $response->assertStatus(200);
    }
}
