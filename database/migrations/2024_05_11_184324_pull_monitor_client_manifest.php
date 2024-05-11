<?php

use Illuminate\Database\Migrations\Migration;

use App\Jobs\FetchClientManifest;

class PullMonitorClientManifest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FetchClientManifest::dispatchNow();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
