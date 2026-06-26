<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stagiaires', function (Blueprint $table) {
            $table->string('cef', 32)->primary();
            $table->foreignId('groupe_id')->constrained('groupes')->restrictOnDelete();
            $table->string('cni', 32)->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('nom_arabe')->nullable();
            $table->string('prenom_arabe')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('genre', 1)->nullable(); // 'F' (Femme) | 'H' (Homme)
            $table->string('telephone', 32)->nullable();
            $table->string('adresse')->nullable();
            $table->string('niveau_scolaire')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['groupe_id', 'nom', 'prenom']);
            $table->index(['actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stagiaires');
    }
};
