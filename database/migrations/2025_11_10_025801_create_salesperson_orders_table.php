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
        Schema::create('salesperson_orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('postalcode_id');
            $table->unsignedBigInteger('employee_id');

            $table-> foreign('postalcode_id')
            ->references('id')
            ->on('postal_codes')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            
            $table-> foreign('employee_id')
            ->references('id')
            ->on('employees')
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
        Schema::dropIfExists('salesperson_orders');
    }
};
