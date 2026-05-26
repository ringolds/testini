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
        Schema::table('banks', function (Blueprint $table) {
            $table->dropUnique(['name', 'user_id']);
            $table->dropColumn('hidden');
            $table->softDeletes();
            $table->unique(['name', 'user_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->dropUnique(['name', 'user_id', 'deleted_at']);
            $table->dropSoftDeletes();
            $table->boolean('hidden');
            $table->unique(['name', 'user_id']);
        });
    }
};
