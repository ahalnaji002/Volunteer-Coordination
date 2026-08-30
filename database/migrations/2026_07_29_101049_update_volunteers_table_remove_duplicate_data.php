<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->dropUnique(['email']);
            $table->dropColumn(['name', 'email']);
        });
    }

    public function down(): void
    {
        Schema::table('volunteers', function (Blueprint $table) {
            $table->string('name');
            $table->string('email');
            $table->unique('email');

            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
