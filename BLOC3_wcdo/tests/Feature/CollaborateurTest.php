<?php

namespace Tests\Feature;

use App\Models\Collaborateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CollaborateurTest extends TestCase
{
    use RefreshDatabase;

    protected Collaborateur $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Collaborateur::factory()->admin()->create();
    }

    public function test_creation_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin)
            ->from('/collaborateurs/create')
            ->post('/collaborateurs', [])
            ->assertSessionHasErrors(['nom', 'prenom', 'email', 'date_premiere_embauche']);
    }

    public function test_email_must_be_unique(): void
    {
        Collaborateur::factory()->create(['email' => 'jean@wacdo.local']);

        $this->actingAs($this->admin)
            ->from('/collaborateurs/create')
            ->post('/collaborateurs', [
                'nom'                    => 'Dupont',
                'prenom'                 => 'Jean',
                'email'                  => 'jean@wacdo.local',
                'date_premiere_embauche' => '2024-01-01',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_admin_collaborateur_requires_password(): void
    {
        $this->actingAs($this->admin)
            ->from('/collaborateurs/create')
            ->post('/collaborateurs', [
                'nom'                    => 'Admin2',
                'prenom'                 => 'Test',
                'email'                  => 'admin2@wacdo.local',
                'date_premiere_embauche' => '2024-01-01',
                'administrateur'         => '1',
                'password'               => '',
            ])
            ->assertSessionHasErrors('password');
    }

    public function test_password_is_hashed_never_stored_in_clear(): void
    {
        $this->actingAs($this->admin)
            ->post('/collaborateurs', [
                'nom'                    => 'Marie',
                'prenom'                 => 'Curie',
                'email'                  => 'marie@wacdo.local',
                'date_premiere_embauche' => '2024-01-01',
                'administrateur'         => '1',
                'password'               => 'NouveauMotDePasse1!',
            ]);

        $collab = Collaborateur::where('email', 'marie@wacdo.local')->firstOrFail();
        $this->assertNotSame('NouveauMotDePasse1!', $collab->password);
        $this->assertTrue(Hash::check('NouveauMotDePasse1!', $collab->password));
    }
}
