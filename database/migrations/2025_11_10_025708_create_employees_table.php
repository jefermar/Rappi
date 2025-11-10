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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('postalcode_id');
            $table->unsignedBigInteger('position_id');

            $table-> foreign('postalcode_id')
            ->references('id')
            ->on('postal_codes')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            
            $table-> foreign('position_id')
            ->references('id')
            ->on('positions')
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
        Schema::dropIfExists('employees');
    }
};
