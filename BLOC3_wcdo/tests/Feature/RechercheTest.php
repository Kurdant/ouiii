<?php

namespace Tests\Feature;

use App\Models\Affectation;
use App\Models\Collaborateur;
use App\Models\Fonction;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RechercheTest extends TestCase
{
    use RefreshDatabase;

    protected Collaborateur $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Collaborateur::factory()->admin()->create();
    }

    public function test_non_affectes_scope_excludes_collaborateurs_with_current_affectation(): void
    {
        $affecte = Collaborateur::factory()->create();
        Affectation::factory()->enCours()->create(['collaborateur_id' => $affecte->id]);

        $nonAffecte = Collaborateur::factory()->create();

        $ids = Collaborateur::nonAffectes()->pluck('id')->all();

        $this->assertContains($nonAffecte->id, $ids);
        $this->assertNotContains($affecte->id, $ids);
    }

    public function test_search_affectations_filters_by_ville(): void
    {
        $r1 = Restaurant::factory()->create(['ville' => 'Marseille']);
        $r2 = Restaurant::factory()->create(['ville' => 'Toulouse']);
        Affectation::factory()->enCours()->create(['restaurant_id' => $r1->id]);
        Affectation::factory()->enCours()->create(['restaurant_id' => $r2->id]);

        $response = $this->actingAs($this->admin)
            ->get('/affectations?ville=marseille&statut=en_cours');

        $response->assertOk();
        $response->assertSee('Marseille');
        $response->assertDontSee('Toulouse');
    }

    public function test_search_affectations_filters_by_fonction(): void
    {
        $f1 = Fonction::factory()->create(['intitule_poste' => 'Manager']);
        $f2 = Fonction::factory()->create(['intitule_poste' => '\u00c9quipier']);
        Affectation::factory()->enCours()->create(['fonction_id' => $f1->id]);
        Affectation::factory()->enCours()->create(['fonction_id' => $f2->id]);

        $response = $this->actingAs($this->admin)
            ->get('/affectations?fonction_id='.$f1->id);

        $response->assertOk();
        $response->assertSee('Manager');
        $response->assertDontSee('\u00c9quipier');
    }
}
