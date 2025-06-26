<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServerTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_tag', function (Blueprint $table) {
            $table->unsignedInteger("server_id");
            $table->foreign("server_id")
                    ->references("id")->on("servers")
                    ->cascadeOnDelete();


            $table->unsignedBigInteger("tag_id");
            $table->foreign("tag_id")
                    ->references("id")->on("tags")
                    ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_tag');
    }
}
