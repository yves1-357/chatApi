<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            //
            // Ajoute user_id après l'id, avec contrainte et suppression en cascade

            if(! Schema::hasColumn('conversations','user_id'))
                {
            $table->foreignId('user_id')
            ->nullable()
                  ->after('id')
                  ->constrained()
                  ->onDelete('cascade');
                }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            //
             // Supprime la colonne et sa contrainte
             if (Schema::hasColumn('conversations', 'user_id')){
            $table->dropConstrainedForeignId('user_id');
             }

        });
    }
};
