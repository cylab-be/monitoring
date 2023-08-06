<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableStatusChanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_changes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer("time")->index();
            $table->integer("server_id");
            $table->integer("status");
            $table->integer("record_id");
            
            $table->foreign("server_id")->references("id")->on("servers");
            $table->foreign("record_id")->references("id")->on("records");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_changes');
    }
}
