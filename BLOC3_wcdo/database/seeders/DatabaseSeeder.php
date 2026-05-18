<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Amorçage de l'application :
     *  - `AdminCollaborateurSeeder` : compte technique admin, obligatoire.
     *  - `DemoSeeder` : jeu de données pour la soutenance (idempotent).
     *
     * Lancement complet : `php artisan db:seed`.
     * Lancement minimal : `php artisan db:seed --class=AdminCollaborateurSeeder`.
     */
    public function run(): void
    {
        $this->call([
            AdminCollaborateurSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
