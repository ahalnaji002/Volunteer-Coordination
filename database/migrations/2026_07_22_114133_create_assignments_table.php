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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('volunteer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('work_location_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('task_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('assignment_date');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};