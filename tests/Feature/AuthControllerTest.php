<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_redirect_stores_state_and_redirects_to_battlenet(): void
    {
        $response = $this->get('/auth/battlenet/redirect?platform=mobile');

        $response->assertRedirect();
        $this->assertStringContainsString('battle.net', $response->headers->get('Location'));
    }

    public function test_redirect_defaults_to_web_platform(): void
    {
        $response = $this->get('/auth/battlenet/redirect');

        $response->assertRedirect();
    }

    public function test_callback_returns_error_when_state_is_missing(): void
    {
        config(['services.auth.redirect_web' => 'http://localhost:8081/auth/callback']);

        $response = $this->get('/auth/battlenet/callback');

        $response->assertRedirect();
        $this->assertStringContainsString('error=missing_state', $response->headers->get('Location'));
    }

    public function test_callback_returns_error_when_state_is_invalid(): void
    {
        config(['services.auth.redirect_web' => 'http://localhost:8081/auth/callback']);

        $response = $this->get('/auth/battlenet/callback?state=invalid_state');

        $response->assertRedirect();
        $this->assertStringContainsString('error=invalid_state', $response->headers->get('Location'));
    }

    public function test_callback_creates_user_and_returns_token_for_web(): void
    {
        $state = 'test_state_123';
        Cache::put("oauth_state:{$state}", 'web', now()->addMinutes(10));

        config(['services.auth.redirect_web' => 'http://localhost:8081/auth/callback']);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('12345678');
        $socialiteUser->shouldReceive('getRaw')->andReturn([
            'sub' => 'test-sub-uuid',
            'battletag' => 'TestPlayer#1234',
        ]);

        Socialite::shouldReceive('driver')
            ->with('battlenet')
            ->andReturnSelf();
        Socialite::shouldReceive('stateless')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andReturn($socialiteUser);

        $response = $this->get("/auth/battlenet/callback?state={$state}&code=test_code");

        $response->assertRedirect();
        $this->assertStringContainsString('http://localhost:8081/auth/callback?code=', $response->headers->get('Location'));

        $this->assertDatabaseHas('users', [
            'battlenet_id' => '12345678',
            'sub' => 'test-sub-uuid',
            'battletag' => 'TestPlayer#1234',
        ]);
    }

    public function test_callback_creates_user_and_returns_token_for_mobile(): void
    {
        $state = 'test_state_456';
        Cache::put("oauth_state:{$state}", 'mobile', now()->addMinutes(10));

        config(['services.auth.redirect_mobile' => 'owc://auth/callback']);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('87654321');
        $socialiteUser->shouldReceive('getRaw')->andReturn([
            'sub' => 'mobile-sub-uuid',
            'battletag' => 'MobilePlayer#5678',
        ]);

        Socialite::shouldReceive('driver')
            ->with('battlenet')
            ->andReturnSelf();
        Socialite::shouldReceive('stateless')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andReturn($socialiteUser);

        $response = $this->get("/auth/battlenet/callback?state={$state}&code=test_code");

        $response->assertRedirect();
        $this->assertStringContainsString('owc://auth/callback?code=', $response->headers->get('Location'));

        $this->assertDatabaseHas('users', [
            'battlenet_id' => '87654321',
            'battletag' => 'MobilePlayer#5678',
        ]);
    }

    public function test_callback_updates_existing_user(): void
    {
        $existingUser = User::factory()->create([
            'battlenet_id' => '12345678',
            'sub' => 'old-sub',
            'battletag' => 'OldName#1111',
        ]);

        $state = 'test_state_789';
        Cache::put("oauth_state:{$state}", 'web', now()->addMinutes(10));

        config(['services.auth.redirect_web' => 'http://localhost:8081/auth/callback']);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('12345678');
        $socialiteUser->shouldReceive('getRaw')->andReturn([
            'sub' => 'new-sub',
            'battletag' => 'NewName#2222',
        ]);

        Socialite::shouldReceive('driver')
            ->with('battlenet')
            ->andReturnSelf();
        Socialite::shouldReceive('stateless')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andReturn($socialiteUser);

        $response = $this->get("/auth/battlenet/callback?state={$state}&code=test_code");

        $response->assertRedirect();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'battlenet_id' => '12345678',
            'sub' => 'new-sub',
            'battletag' => 'NewName#2222',
        ]);
    }

    public function test_user_endpoint_returns_authenticated_user(): void
    {
        $user = User::factory()->create([
            'battletag' => 'AuthUser#9999',
        ]);

        $response = $this->actingAs($user)->getJson('/api/auth/user');

        $response->assertOk();
        $response->assertJson([
            'battletag' => 'AuthUser#9999',
        ]);
    }

    public function test_user_endpoint_returns_401_when_unauthenticated(): void
    {
        $response = $this->getJson('/api/auth/user');

        $response->assertUnauthorized();
    }

    public function test_logout_deletes_current_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('app')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout');

        $response->assertOk();
        $response->assertJson(['message' => 'Logged out']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_returns_401_when_unauthenticated(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertUnauthorized();
    }

    public function test_callback_redirects_with_error_when_oauth_fails(): void
    {
        $state = 'test_state_error';
        Cache::put("oauth_state:{$state}", 'web', now()->addMinutes(10));

        config(['services.auth.redirect_web' => 'http://localhost:8081/auth/callback']);

        Socialite::shouldReceive('driver')
            ->with('battlenet')
            ->andReturnSelf();
        Socialite::shouldReceive('stateless')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andThrow(new \Exception('OAuth failed'));

        $response = $this->get("/auth/battlenet/callback?state={$state}&code=test_code");

        $response->assertRedirect();
        $this->assertStringContainsString('error=auth_failed', $response->headers->get('Location'));
        $this->assertStringContainsString('http://localhost:8081/auth/callback', $response->headers->get('Location'));
    }

    public function test_callback_redirects_to_mobile_with_error_when_oauth_fails(): void
    {
        $state = 'test_state_mobile_error';
        Cache::put("oauth_state:{$state}", 'mobile', now()->addMinutes(10));

        config(['services.auth.redirect_mobile' => 'owc://auth/callback']);

        Socialite::shouldReceive('driver')
            ->with('battlenet')
            ->andReturnSelf();
        Socialite::shouldReceive('stateless')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andThrow(new \Exception('OAuth failed'));

        $response = $this->get("/auth/battlenet/callback?state={$state}&code=test_code");

        $response->assertRedirect();
        $this->assertStringContainsString('error=auth_failed', $response->headers->get('Location'));
        $this->assertStringContainsString('owc://auth/callback', $response->headers->get('Location'));
    }

    public function test_callback_treats_expired_state_as_invalid(): void
    {
        config(['services.auth.redirect_web' => 'http://localhost:8081/auth/callback']);

        Cache::put('oauth_state:expired_state', 'web', now()->addMinutes(10));
        $this->travel(11)->minutes();

        $response = $this->get('/auth/battlenet/callback?state=expired_state');

        $response->assertRedirect();
        $this->assertStringContainsString('error=invalid_state', $response->headers->get('Location'));
    }

    public function test_callback_redirects_non_web_platform_to_mobile(): void
    {
        $state = 'test_state_android';
        Cache::put("oauth_state:{$state}", 'android', now()->addMinutes(10));

        config(['services.auth.redirect_mobile' => 'owc://auth/callback']);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getId')->andReturn('55667788');
        $socialiteUser->shouldReceive('getRaw')->andReturn([
            'sub' => 'android-sub-uuid',
            'battletag' => 'AndroidPlayer#9012',
        ]);

        Socialite::shouldReceive('driver')
            ->with('battlenet')
            ->andReturnSelf();
        Socialite::shouldReceive('stateless')
            ->andReturnSelf();
        Socialite::shouldReceive('user')
            ->andReturn($socialiteUser);

        $response = $this->get("/auth/battlenet/callback?state={$state}&code=test_code");

        $response->assertRedirect();
        $this->assertStringContainsString('owc://auth/callback?code=', $response->headers->get('Location'));
    }

    public function test_exchange_returns_a_working_token_for_a_valid_code(): void
    {
        $user = User::factory()->create(['battletag' => 'Exchanger#4242']);
        Cache::put('auth_code:valid_code', $user->id, now()->addSeconds(60));

        $response = $this->postJson('/api/auth/exchange', ['code' => 'valid_code']);

        $response->assertOk();
        $token = $response->json('token');
        $this->assertNotEmpty($token);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/auth/user')
            ->assertOk()
            ->assertJson(['battletag' => 'Exchanger#4242']);
    }

    public function test_exchange_code_is_single_use(): void
    {
        $user = User::factory()->create();
        Cache::put('auth_code:once_code', $user->id, now()->addSeconds(60));

        $this->postJson('/api/auth/exchange', ['code' => 'once_code'])->assertOk();
        $this->postJson('/api/auth/exchange', ['code' => 'once_code'])->assertUnauthorized();
    }

    public function test_exchange_returns_401_for_invalid_code(): void
    {
        $response = $this->postJson('/api/auth/exchange', ['code' => 'does_not_exist']);

        $response->assertUnauthorized();
    }

    public function test_exchange_returns_422_when_code_is_missing(): void
    {
        $response = $this->postJson('/api/auth/exchange', []);

        $response->assertStatus(422);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
