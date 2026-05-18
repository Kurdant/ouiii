<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collaborateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->string('email', 180)->unique();
            $table->string('telephone', 20)->nullable();
            $table->date('date_premiere_embauche');
            $table->boolean('administrateur')->default(false);
            $table->string('password', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('nom');
            $table->index('prenom');
        });

        // CDC : un administrateur doit obligatoirement disposer d'un mot de passe.
        DB::statement(<<<'SQL'
            ALTER TABLE collaborateurs
            ADD CONSTRAINT collaborateurs_admin_requires_password
            CHECK (administrateur = false OR password IS NOT NULL)
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborateurs');
    }
};
