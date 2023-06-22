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
        Schema::create('debtors', function (Blueprint $table) {
            $table->id();
            $table->string('debtor_number');
            $table->string('name_1')->nullable();
            $table->string('name_2')->nullable();
            $table->string('search_name')->nullable();
            $table->string('address')->nullable();
            $table->string('postalcode')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('contact')->nullable();
            $table->string('phonenumber')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('email_cc')->nullable();
            $table->string('email_invoice')->nullable();
            $table->string('email_invoice_cc')->nullable();
            $table->string('tax_number')->nullable();
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
        Schema::dropIfExists('debtors');
    }
};
