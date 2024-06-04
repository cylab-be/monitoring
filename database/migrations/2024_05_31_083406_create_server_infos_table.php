<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Server;

class CreateServerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('server_infos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            
            $table->unsignedInteger("server_id");
            $table->foreign("server_id")->references("id")->on("servers")
                    ->onDelete("cascade");
            
            // uptime in seconds
            $table->integer("uptime")->default(0);
            $table->string("uuid", 36)->default("");
            $table->string("lsb", 128)->default("");
            $table->string("manufacturer", 128)->default("");
            $table->string("product", 128)->default("");
            $table->integer("memory")->default(0);
            $table->string("client_version", 128)->default("");
            
            // will be casted to arrays
            // default JSON value requires mysql 8 !!
            $table->string("cpuinfo")->default("[]");
            $table->string("addresses")->default("[]");
        });
        
        // create for existing servers
        foreach (Server::all() as $server)
        {
            /** @var Server $server */
            $server->info()->create();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('server_infos');
    }
}
