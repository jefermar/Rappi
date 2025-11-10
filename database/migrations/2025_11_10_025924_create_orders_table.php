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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('shipping_method_id');
            $table->unsignedBigInteger('salesperson_order_id');

            $table-> foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');
            
            $table-> foreign('shipping_method_id')
            ->references('id')
            ->on('shipping_methods')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table-> foreign('salesperson_order_id')
            ->references('id')
            ->on('salesperson_orders')
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
        Schema::dropIfExists('orders');
    }
};
