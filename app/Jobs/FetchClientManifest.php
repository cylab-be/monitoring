<?php

namespace App\Jobs;

use GuzzleHttp\Client;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class FetchClientManifest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    const CLIENT_VERSION = "CLIENT_VERSION";
    const CLIENT_URL = "CLIENT_URL";

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        logger()->info("Fetch client manifest");
        
        $MANIFEST_URL = "https://download.cylab.be/monitor-php-client/manifest.json";
        $options = ['timeout' => 5.0];
        $client = new Client($options);
        $json = $client->get($MANIFEST_URL)->getBody();
        $manifest = json_decode($json, true)[0];
        
        Cache::put(self::CLIENT_VERSION, $manifest["version"], 3600 * 24);
        Cache::put(self::CLIENT_URL, $manifest["url"], 3600 * 24);
    }
    
    public static function version() : ?string
    {
        return Cache::get(self::CLIENT_VERSION, null);
    }

    public static function url() : ?string
    {
        return Cache::get(self::CLIENT_URL, null);
    }
}
