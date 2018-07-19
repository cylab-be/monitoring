<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $fillable = ['name'];
    public $sensors;
    protected $primaryKey = 'id';
    public $lastState;
    public function organization()
    {
        return $this->belongsTo('App\Models\Organizations');
    }

    public function sensors()
    {
        $sensors = Sensors::where("token","".$this->token)->get();
        foreach($sensors as $sensor){
            $sensor = $this->fetchSensor($sensor);
        }

        return $sensors;
    }

    public function getLastState()
    {
        $sensor = Sensors::where("token","".$this->token)->orderBy('created_at', 'desc')->first();
        $lastState = $this->fetchSensor($sensor);
        $lastState["diskOk"]=true;
        if(array_key_exists("disks" , $lastState )){
            foreach($lastState["disks"] as $disk){
                if($disk["usedpercent"]>70)$lastState["diskOk"]=false;
            }
    }

        return $lastState;
    }
    public function fetchSensor($sensor){
        $sensor["content"] = json_decode($sensor["content"]);
        if($sensor["content"]!=null){
            //INODES
            $val = preg_match_all("/\\n([A-z\/0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/", $sensor["content"]->{"Inodes"}, $output_array);
            $Inodes = array();
            $size = count($output_array[0]);
            for($i = 0; $i < $size; ++$i) {
                $Inode['sys']=$output_array[1][$i];
                $Inode['inodes']=$output_array[2][$i];
                $Inode['iutil']=$output_array[3][$i];
                $Inode['ilibre']=$output_array[4][$i];
                $Inode['iutipercent']=$output_array[5][$i];
                $Inode['mounted']=$output_array[6][$i];
                $Inodes[] = $Inode;
            }
            $sensor["inodes"] = $Inodes;
            //DISKS
            $val = preg_match_all("/\\n([A-z\/0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)\s*([0-9]+)%\s*([A-z\/0-9]+)/", $sensor["content"]->{"Disk"}, $output_array);
            $Disks = array();
            $size = count($output_array[0]);
            for($i = 0; $i < $size; ++$i) {
                $Disk['sys']=$output_array[1][$i];
                $Disk['blocs']=$output_array[2][$i];
                $Disk['used']=$output_array[3][$i];
                $Disk['free']=$output_array[4][$i];
                $Disk['usedpercent']=$output_array[5][$i];
                $Disk['mounted']=$output_array[6][$i];
                $Disks[] = $Disk;
            }
            $sensor["disks"] = $Disks;
            //var_dump($Disks);
            //UDP
            preg_match_all("/\\n(udp[0-9]*) *([0-9]+)* *([0-9]+)* *([0-9:.A-z]+)* *([0-9A-z.:\*]+)* *([0-9]+)\/([A-z-:]+[ ][A-z]*)/", $sensor["content"]->{"UDP"}, $output_array);
            $udps = array();
            $size = count($output_array[0]);
            for($i = 0; $i < $size; ++$i) {
                $UDP['proto']=$output_array[1][$i];
                $UDP['recv']=$output_array[2][$i];
                $UDP['send']=$output_array[3][$i];
                $UDP['local']=$output_array[4][$i];
                $UDP['remote']=$output_array[5][$i];
                $UDP['state']=$output_array[6][$i];
                $UDP['program']=$output_array[7][$i];
                $udps[] = $UDP;
            }
            $sensor["udp"] = $udps;
            //TCP
            preg_match_all("/\\n(tcp[0-9]*) *([0-9]+) *([0-9]+) *([0-9.:]+) *([0-9.:\*]+) *([A-z]+) *([0-9]+\/[A-z0-9]+)*/", $sensor["content"]->{"TCP"}, $output_array);
            $tcps = array();
            $size = count($output_array[0]);
            for($i = 0; $i < $size; ++$i) {
                $TCP['proto']=$output_array[1][$i];
                $TCP['recv']=$output_array[2][$i];
                $TCP['send']=$output_array[3][$i];
                $TCP['local']=$output_array[4][$i];
                $TCP['remote']=$output_array[5][$i];
                $TCP['state']=$output_array[6][$i];
                $TCP['program']=$output_array[7][$i];
                $tcps[] = $TCP;
            }
            $sensor["tcp"] = $tcps;
            //NETWORK
            $interfaces = array();
            foreach (preg_split("/\r?\n\r?\n/s", $sensor["content"]->{"Network"}) as $int) {
                preg_match("/^([A-z]*\d*[A-z]\d*):\sflags=([0-9]+)<([A-z,]+)>*\s\smtu\s([0-9]+).*\s+inet\s([0-9.]+)\s\snetmask\s([0-9.]+)(  broadcast ([0-9.]+))*.*\s+inet6\s([0-9A-z:]+)\s\sprefixlen\s([0-9]+)\s\sscopeid\s([A-z 0-9 < >]+)\s+(ether|loop) *(([A-z0-9:]*))\s{2}txqueuelen\s([0-9]+)\s{2}\(([A-z ]+)\).*\s+RX packets ([0-9]+)  bytes ([0-9 A-z \(\) .]+)\s+RX errors ([0-9]+)  dropped ([0-9]+)  overruns ([0-9]+)  frame ([0-9]+).*\s+TX packets ([0-9]+)  bytes ([0-9 A-z \(\) .]+)\s+TX errors ([0-9]+)  dropped ([0-9]+) overruns ([0-9]+)  carrier ([0-9]+)  collisions ([0-9]+)/", $int, $regex);

                if (!empty($regex)) {
                    $interface = array();
                    $interface['name'] = $regex[1];
                    $interface['flags'] = $regex[2];
                    $interface['state'] = $regex[3];
                    $interface['mtu'] = $regex[4];
                    $interface['ipv4'] = $regex[5];
                    $interface['netmask'] = $regex[6];
                    $interface['broadcast'] = $regex[8];
                    $interface['ipv6'] = $regex[9];
                    $interface['prefix'] = $regex[10];
                    $interface['scope'] = $regex[11];
                    $interface['type'] = $regex[12];
                    $interface['mac'] = $regex[14];
                    $interface['txqueu'] = $regex[15];
                    $interface['type2'] = $regex[16];
                    $interface['rx']['packets'] = (int)$regex[17];
                    $interface['rx']['bytes'] = $regex[18];//(int)
                    $interface['rx']['errors'] = (int)$regex[19];
                    $interface['rx']['dropped'] = (int)$regex[20];
                    $interface['rx']['overruns'] = (int)$regex[21];
                    $interface['rx']['frame'] = (int)$regex[22];

                    //$interface['rx']['hbytes'] = (int) $regex[20];

                    $interface['tx']['packets'] = (int)$regex[23];
                    $interface['tx']['bytes'] = $regex[24]; //(int)
                    $interface['tx']['errors'] = (int)$regex[25];
                    $interface['tx']['dropped'] = (int)$regex[26];
                    $interface['tx']['overruns'] = (int)$regex[27];
                    $interface['tx']['carrier'] = (int)$regex[28];
                    //$interface['tx']['collisions'] = (int) $regex[28];

                    //$interface['tx']['hbytes'] = (int) $regex[22];

                    $interfaces[] = $interface;
                }
                $sensor["networks"] = $interfaces;
            }
        }
        return $sensor;
    }
}
