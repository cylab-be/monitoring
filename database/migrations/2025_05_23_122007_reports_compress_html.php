<?php

use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportsCompressHtml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // process reports by groups of 10
        // preserving ID
        // https://laravel.com/docs/12.x/queries#chunking-results
        DB::table('reports')->chunkById(10, function (Collection $reports) {
            foreach ($reports as $report) {
                $html = $report->html;
                $compressed = base64_encode(gzdeflate($html));
                DB::table('reports')
                        ->where("id", $report->id)
                        ->update(['html' => $compressed]);
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
        DB::table('reports')->chunkById(10, function (Collection $reports) {
            foreach ($reports as $report) {
                $compressed = $report->html;
                $html = gzinflate(base64_decode($compressed));
                DB::table('reports')
                        ->where("id", $report->id)
                        ->update(['html' => $html]);
            }

            echo ".";
        });
    }
}
