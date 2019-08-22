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
            
            $table->integer('source_region_id')->nullable();
            $table->integer('shop_id')->nullable();

            $table->integer('stock_location_id')->nullable();
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

        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('score');
            $table->timestamp('created_at');
        });

        Schema::create('shop_rating', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('rating_id');
            $table->integer('shop_id');
        });


        Schema::create('stock_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('aisle_id');
            $table->integer('shop_id');
            $table->timestamps();
        });

        Schema::create('aisles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('aisle_code');
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
