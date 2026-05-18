<?php

namespace Database\Seeders;

use App\Models\Affectation;
use App\Models\Collaborateur;
use App\Models\Fonction;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Jeu de démonstration pour la soutenance.
 *
 * - 5 fonctions
 * - 3 restaurants répartis sur 3 villes
 * - 7 collaborateurs opérationnels (1 volontairement non affecté)
 * - Affectations : en cours, future, terminée
 *
 * Idempotent : `firstOrCreate` sur les référentiels, `Affectation::count()`
 * pour ne pas dupliquer la table métier en cas de relance.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $fonctions = collect([
                'Manager', 'Équipier polyvalent', 'Cuisinier', 'Caissier', 'Adjoint de direction',
            ])->mapWithKeys(fn ($nom) => [
                $nom => Fonction::firstOrCreate(['intitule_poste' => $nom]),
            ]);

            $restos = [
                'paris' => Restaurant::firstOrCreate(
                    ['nom' => 'Wacdo Paris Opéra'],
                    ['adresse' => '10 boulevard des Capucines', 'code_postal' => '75009', 'ville' => 'Paris']
                ),
                'lyon' => Restaurant::firstOrCreate(
                    ['nom' => 'Wacdo Lyon Bellecour'],
                    ['adresse' => '3 place Bellecour', 'code_postal' => '69002', 'ville' => 'Lyon']
                ),
                'marseille' => Restaurant::firstOrCreate(
                    ['nom' => 'Wacdo Marseille Vieux-Port'],
                    ['adresse' => '25 quai du Port', 'code_postal' => '13002', 'ville' => 'Marseille']
                ),
            ];

            $operationnels = collect([
                ['nom' => 'Martin',  'prenom' => 'Sophie', 'email' => 'sophie.martin@wacdo.local', 'embauche' => '2022-03-15'],
                ['nom' => 'Bernard', 'prenom' => 'Luc',    'email' => 'luc.bernard@wacdo.local',   'embauche' => '2023-09-01'],
                ['nom' => 'Dubois',  'prenom' => 'Amina',  'email' => 'amina.dubois@wacdo.local',  'embauche' => '2024-01-10'],
                ['nom' => 'Petit',   'prenom' => 'Karim',  'email' => 'karim.petit@wacdo.local',   'embauche' => '2021-06-20'],
                ['nom' => 'Robert',  'prenom' => 'Emma',   'email' => 'emma.robert@wacdo.local',   'embauche' => '2024-09-05'],
                ['nom' => 'Richard', 'prenom' => 'Noé',    'email' => 'noe.richard@wacdo.local',   'embauche' => '2023-02-14'],
                // Non affecté volontairement pour la démonstration "Collaborateurs non affectés".
                ['nom' => 'Moreau',  'prenom' => 'Léa',    'email' => 'lea.moreau@wacdo.local',    'embauche' => '2025-01-02'],
            ])->map(fn ($data) => Collaborateur::firstOrCreate(
                ['email' => $data['email']],
                [
                    'nom'                    => $data['nom'],
                    'prenom'                 => $data['prenom'],
                    'date_premiere_embauche' => $data['embauche'],
                    'administrateur'         => false,
                    'password'               => null,
                ]
            ))->values();

            if (Affectation::count() > 0) {
                return;
            }

            $today = Carbon::today();

            // Trois affectations en cours.
            Affectation::create([
                'collaborateur_id' => $operationnels[0]->id,
                'restaurant_id'    => $restos['paris']->id,
                'fonction_id'      => $fonctions['Manager']->id,
                'date_debut'       => $today->copy()->subMonths(6)->toDateString(),
                'date_fin'         => null,
            ]);
            Affectation::create([
                'collaborateur_id' => $operationnels[1]->id,
                'restaurant_id'    => $restos['paris']->id,
                'fonction_id'      => $fonctions['Équipier polyvalent']->id,
                'date_debut'       => $today->copy()->subMonths(3)->toDateString(),
                'date_fin'         => null,
            ]);
            Affectation::create([
                'collaborateur_id' => $operationnels[3]->id,
                'restaurant_id'    => $restos['marseille']->id,
                'fonction_id'      => $fonctions['Adjoint de direction']->id,
                'date_debut'       => $today->copy()->subYear()->toDateString(),
                'date_fin'         => null,
            ]);

            // Une affectation en cours avec date de fin future (CDD).
            Affectation::create([
                'collaborateur_id' => $operationnels[2]->id,
                'restaurant_id'    => $restos['lyon']->id,
                'fonction_id'      => $fonctions['Cuisinier']->id,
                'date_debut'       => $today->copy()->subMonths(2)->toDateString(),
                'date_fin'         => $today->copy()->addMonths(2)->toDateString(),
            ]);

            // Une affectation future (planifiée).
            Affectation::create([
                'collaborateur_id' => $operationnels[4]->id,
                'restaurant_id'    => $restos['lyon']->id,
                'fonction_id'      => $fonctions['Caissier']->id,
                'date_debut'       => $today->copy()->addMonth()->toDateString(),
                'date_fin'         => null,
            ]);

            // Une affectation terminée (historique).
            Affectation::create([
                'collaborateur_id' => $operationnels[5]->id,
                'restaurant_id'    => $restos['marseille']->id,
                'fonction_id'      => $fonctions['Équipier polyvalent']->id,
                'date_debut'       => $today->copy()->subYears(2)->toDateString(),
                'date_fin'         => $today->copy()->subMonths(8)->toDateString(),
            ]);
        });
    }
}
