<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_summaries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger("server_id");
            $table->bigInteger("time")->index();
            $table->integer("status_code");
            // json encoded list of report_id, to avoid a separate many-to-many table
            $table->text("reports");
            
            $table->foreign("server_id")->references("id")->on("servers")
                    ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_summaries');
    }
}
