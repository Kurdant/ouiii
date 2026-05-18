<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 150);
            $table->string('adresse', 255);
            $table->string('code_postal', 10);
            $table->string('ville', 100);
            $table->timestamps();

            $table->index('ville');
            $table->index('code_postal');
            $table->index('nom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
