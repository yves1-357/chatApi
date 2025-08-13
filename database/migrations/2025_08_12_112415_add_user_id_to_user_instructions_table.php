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
        Schema::table('user_instructions', function (Blueprint $table) {
            if (!Schema::hasColumn('user_instructions', 'user_id')) {
        $table->foreignId('user_id')
            ->after('id')
            ->constrained()
            ->onDelete('cascade')
            ->unique();
    }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_instructions', function (Blueprint $table) {
            //
        });
    }
};
