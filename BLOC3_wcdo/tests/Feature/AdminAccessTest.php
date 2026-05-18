<?php

namespace Tests\Feature;

use App\Models\Collaborateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_collaborateur_with_password_cannot_access_dashboard(): void
    {
        // Cas r\u00e9siduel : un collaborateur connect\u00e9 mais non administrateur
        // doit \u00eatre rejet\u00e9 par EnsureUserIsAdmin avec un 403.
        $user = Collaborateur::factory()->create([
            'administrateur' => false,
            'password'       => 'TestUserPassword1!',
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertForbidden();

        $this->assertGuest(); // Le middleware a d\u00e9connect\u00e9 l'utilisateur.
    }

    public function test_admin_can_access_referentiels(): void
    {
        $admin = Collaborateur::factory()->admin()->create();

        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $this->actingAs($admin)->get('/restaurants')->assertOk();
        $this->actingAs($admin)->get('/collaborateurs')->assertOk();
        $this->actingAs($admin)->get('/fonctions')->assertOk();
        $this->actingAs($admin)->get('/affectations')->assertOk();
    }

    public function test_guest_is_redirected_from_all_admin_routes(): void
    {
        foreach (['/dashboard', '/restaurants', '/collaborateurs', '/fonctions', '/affectations'] as $route) {
            $this->get($route)->assertRedirect('/login');
        }
    }
}
