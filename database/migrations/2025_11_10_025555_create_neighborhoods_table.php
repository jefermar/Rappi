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
        Schema::create('neighborhoods', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('state_id');

            $table-> foreign('city_id')
            ->references('id')
            ->on('cities')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            
            $table-> foreign('state_id')
            ->references('id')
            ->on('states')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('neighborhoods');
    }
};
