<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('affectations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('collaborateur_id')
                ->constrained('collaborateurs')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('restaurant_id')
                ->constrained('restaurants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('fonction_id')
                ->constrained('fonctions')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->date('date_debut');
            $table->date('date_fin')->nullable();

            $table->timestamps();

            $table->index(['collaborateur_id', 'date_debut']);
            $table->index(['restaurant_id', 'date_debut']);
            $table->index(['fonction_id', 'date_debut']);
        });

        // CDC : date_fin doit être vide ou supérieure ou égale à date_debut.
        DB::statement(<<<'SQL'
            ALTER TABLE affectations
            ADD CONSTRAINT affectations_date_fin_after_date_debut
            CHECK (date_fin IS NULL OR date_fin >= date_debut)
        SQL);

        // CDC : doublon strict interdit (même collaborateur, restaurant, fonction,
        // date_debut, date_fin). NULLS NOT DISTINCT pour qu'un même NULL sur date_fin
        // soit considéré comme une collision (sinon PostgreSQL traite NULL != NULL).
        DB::statement(<<<'SQL'
            CREATE UNIQUE INDEX affectations_doublon_strict_unique
            ON affectations (collaborateur_id, restaurant_id, fonction_id, date_debut, date_fin)
            NULLS NOT DISTINCT
        SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('affectations');
    }
};
