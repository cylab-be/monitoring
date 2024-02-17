<?php

use App\User;
use App\Organization;
use App\Server;
use App\Record;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $servers_count = 10;
        $days = 1;
        
        $email = Str::lower(Str::random(8)) . "@example.com";
        $password = "password";
        
        echo "Created new user ...\n";
        echo "* email: $email\n";
        echo "* password: $password\n\n";
        
        $user = new User();
        $user->name = Str::random(10);
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->save();
        
        echo "Create organization ...\n\n";
        
        $org = new Organization();
        $org->name = "Cylab";
        $org->save();
        
        $org->users()->attach($user->id);
        
        echo "Create $servers_count servers ...\n\n";
        
        $servers = factory(Server::class, $servers_count)->make();
        foreach ($servers as $server) {
            /** @var Server $server */
            $server->organization_id = $org->id;
            $server->save();
        }
        
        $this->records_total = round(3600 * 24 * $days / 300 * $servers_count) * count(self::$records);
        echo "Create " . $this->records_total . " records...\n";
        
        $start_time = time() - 3600 * 24 * $days;
        $time = $start_time;
        $this->records_count = 0;
        while ($time < time()) {
            foreach ($servers as $server) {
                $this->records($time, $server);
            }
            
            // 5 minutes
            $time += 5 * 60;
        }
    }
    
    // types of records to create, and corresponding file in /test/Unit
    public static $records = [
        "cpu" => "cpuinfo",
        "loadavg" => "loadavg",
        "ifconfig" => "ifconfig",
        "memory" => "meminfo",
        "cpu-temperature" => "sensors",
        "perccli" => "perccli",
        "netstat-listen-tcp" => "netstat-tcp",
        "system" => "system"
    ];
    
    
    /**
     * create records for this server
     * @param int $time
     * @param int $server_id
     */
    private function records(int $time, Server $server)
    {
        foreach (self::$records as $label => $file) {
            $record = new Record();
            $record->time = $time;
            $record->server_id = $server->id;
            $record->label = $label;
            $record->data = trim(file_get_contents(__DIR__ . "/../../tests/Unit/" . $file));
            $record->save();
            
            $this->progress();
        }
    }
    
    
    private $records_count = 0;
    
    private function progress()
    {
        $this->records_count++;
        if ($this->records_count % 1000 == 0) {
            echo $this->records_count . "(" . 
                    round($this->records_count / $this->records_total * 100) . "%)\n";
        }
    }
}
