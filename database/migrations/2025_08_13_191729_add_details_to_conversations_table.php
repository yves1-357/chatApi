<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {

            if (!Schema::hasColumn('conversations', 'name')) {
            $table->string('name')->nullable()->after('user_id');
        }

        if (!Schema::hasColumn('conversations', 'email')) {
            $table->string('email')->nullable()->after('name');
        }

        if (!Schema::hasColumn('conversations', 'model')) {
            $table->string('model')->nullable()->after('email');
        }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {

            if (Schema::hasColumn('conversations', 'name')) {
            $table->dropColumn('name');
        }

        if (Schema::hasColumn('conversations', 'email')) {
            $table->dropColumn('email');
        }

        if (Schema::hasColumn('conversations', 'model')) {
            $table->dropColumn('model');
        }
        });
    }
};
