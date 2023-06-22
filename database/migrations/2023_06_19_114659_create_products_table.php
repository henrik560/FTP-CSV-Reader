<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_number');
            $table->string('oms_1')->nullable();
            $table->string('oms_2')->nullable();
            $table->string('oms_3')->nullable();
            $table->string('search_name')->nullable();
            $table->string('group')->nullable();
            $table->string('ean_number')->nullable();
            $table->string('sell_price')->nullable();
            $table->string('unit')->nullable();
            $table->float('unit_price')->nullable();
            $table->integer('stock')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
