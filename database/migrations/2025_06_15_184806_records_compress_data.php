<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecordsCompressData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // process records by groups of 10
        // preserving ID
        // https://laravel.com/docs/12.x/queries#chunking-results
        DB::table('records')->chunkById(100, function (Collection $records) {
            foreach ($records as $record) {
                $data = $record->data;
                $compressed = base64_encode(gzdeflate($data));
                DB::table('records')
                        ->where("id", $record->id)
                        ->update(['data' => $compressed]);
            }

            echo ".";
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
