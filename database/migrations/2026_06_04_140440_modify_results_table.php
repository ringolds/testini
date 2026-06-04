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
        Schema::table('results', function (Blueprint $table) {
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->float('score')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('results', function (Blueprint $table) {
            $table->float('score')->nullable(false)->change();
            $table->dropColumn('end_time');    
            $table->dropColumn('start_time');
        });
    }
};
