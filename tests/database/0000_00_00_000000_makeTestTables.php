<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeTestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('franchise_id')->nullable();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);

            $table->integer('amount');
            $table->integer('value');
            $table->integer('shop_id')->nullable();
        });

        Schema::create('franchises', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('region_id')->nullable();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->text('note');

            $table->integer('noteable_id');
            $table->string('noteable_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
