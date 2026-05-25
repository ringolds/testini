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
        Schema::table('question_components', function (Blueprint $table) {
            $table->unique(['question_id', 'role'], 'uq_question_component_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_components', function (Blueprint $table) {
            $table->dropUnique('uq_question_component_role');
        });
    }
};
