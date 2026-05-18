<?php

namespace Tests\Feature;

use App\Models\Collaborateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CsrfTest extends TestCase
{
    use RefreshDatabase;

    public function test_csrf_middleware_is_active_on_post_login(): void
    {
        // En testing, la middleware VerifyCsrfToken est d\u00e9sactiv\u00e9 par d\u00e9faut.
        // On v\u00e9rifie sa pr\u00e9sence dans la pile de routes.
        $route = Route::getRoutes()->getByName('login.attempt');
        $this->assertNotNull($route, 'La route login.attempt doit exister.');

        // CSRF est appliqu\u00e9 globalement via web middleware group.
        $this->assertContains('web', $route->gatherMiddleware());
    }

    public function test_no_password_is_visible_in_show_response(): void
    {
        $admin = Collaborateur::factory()->admin()->create([
            'email'    => 'admin@wacdo.local',
            'password' => 'MotDePasseSecret1!',
        ]);

        $response = $this->actingAs($admin)->get('/collaborateurs/'.$admin->id);
        $response->assertOk();
        $response->assertDontSee('MotDePasseSecret1!');
        $response->assertDontSee($admin->getAuthPassword());
    }
}
