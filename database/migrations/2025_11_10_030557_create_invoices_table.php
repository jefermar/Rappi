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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
             $table->unsignedBigInteger('payment_method_id');
            $table->unsignedBigInteger('order_id');

            $table-> foreign('payment_method_id')
            ->references('id')
            ->on('payment_methods')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            
            $table-> foreign('order_id')
            ->references('id')
            ->on('orders')
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
        Schema::dropIfExists('invoices');
    }
};
