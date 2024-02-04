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
        Schema::create('table_kicks', function (Blueprint $table) {
            $table->id();
            $table->longText('opponentName');
            $table->longText('punishedName');
            $table->longText('reason');
            $table->json('url')->nullable()->default(null);
            $table->dateTime('timeGenerated');
            $table->boolean('confirmed')->default(false);
            $table->integer('port');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
