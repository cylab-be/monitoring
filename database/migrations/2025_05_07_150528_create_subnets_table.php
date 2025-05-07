<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubnetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subnets', function (Blueprint $table) {
            $table->bigIncrements("id");
            $table->timestamps();
            $table->integer("organization_id")->references("id")->on("organizations");
            
            $table->string("name", 255);
            $table->string("address", 255);
            $table->integer("mask");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subnets');
    }
}
