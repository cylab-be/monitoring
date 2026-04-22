<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table) {
            // delete and recreate foreign id
            //$table->dropForeign(['server_id']);
            
            $table->unsignedBigInteger("rack_id")
                    ->nullable()
                    ->change();
            
            $table->foreign("rack_id")
                    ->references("id")->on("racks") // was ->on("servers") !!!! :-(
                    ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
