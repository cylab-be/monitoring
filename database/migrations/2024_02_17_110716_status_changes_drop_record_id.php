<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StatusChangesDropRecordId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('status_changes', function (Blueprint $table) {
            $table->dropForeign(["record_id"]);
            $table->dropColumn("record_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('status_changes', function (Blueprint $table) {
            //
        });
    }
}
