<?php

namespace Tests\Feature;

use App\Models\Collaborateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        Collaborateur::factory()->admin()->create([
            'email'    => 'admin@wacdo.local',
            'password' => 'MotDePasseTestAdmin1!',
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@wacdo.local',
            'password' => 'MotDePasseTestAdmin1!',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated('web');
    }

    public function test_login_fails_with_generic_message_on_wrong_password(): void
    {
        Collaborateur::factory()->admin()->create([
            'email'    => 'admin@wacdo.local',
            'password' => 'MotDePasseTestAdmin1!',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => 'admin@wacdo.local',
            'password' => 'WrongPassword!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_same_generic_message_on_unknown_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email'    => 'inconnu@wacdo.local',
            'password' => 'Whatever123!',
        ]);

        $response->assertSessionHasErrors('email');
        // Le message d'erreur ne r\u00e9v\u00e8le pas si l'email existe ou non (anti-\u00e9num\u00e9ration).
        $errors = session('errors')->get('email');
        $this->assertSame(['Identifiants invalides.'], $errors);
    }

    public function test_logout_invalidates_session(): void
    {
        $admin = Collaborateur::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/logout')
            ->assertRedirect('/login');

        $this->assertGuest();
    }
}
