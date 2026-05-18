<?php

namespace Database\Seeders;

use App\Models\Collaborateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crée le premier collaborateur administrateur.
 *
 * CDC : le mot de passe n'est jamais stocké en clair (Hash::make).
 * Cet administrateur permet le premier accès à l'application après migration.
 * Les identifiants doivent être modifiés avant tout déploiement réel.
 */
class AdminCollaborateurSeeder extends Seeder
{
    public function run(): void
    {
        Collaborateur::updateOrCreate(
            ['email' => 'admin@wacdo.local'],
            [
                'nom'                    => 'Admin',
                'prenom'                 => 'Wacdo',
                'telephone'              => null,
                'date_premiere_embauche' => '2026-01-01',
                'administrateur'         => true,
                'password'               => Hash::make('AdminWacdo2026!'),
            ]
        );
    }
}
