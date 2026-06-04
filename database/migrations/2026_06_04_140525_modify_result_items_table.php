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
        Schema::table('result_items', function (Blueprint $table) {
            $table->unsignedInteger('order');
            $table->boolean('is_correct')->nullable()->change();
            $table->integer('duration')->nullable()->change(); 
            $table->text('user_answer_content')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('result_items', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->boolean('is_correct')->nullable(false)->change();
            $table->integer('duration')->nullable(false)->change();
            $table->text('user_answer_content')->nullable(false)->change();
        });
    }
};
