<?php

namespace App\Sensor;

/**
 * Description of Temper
 *
 * @author helha
 */
class Temper
{
    public $part1= ""; //eg : 0a
    public $part2= ""; //eg : 6c
    public $temp=array();//eg : [26,28]
    
    public function conversion()
    {
        $hexatemp=$this->part1 . $this->part2; //eg : 0a6c
        $decitemp=hexdec($hexatemp); //eg : 2628
        $this->temp[1]=substr($decitemp,0,-2); //eg : 26
        $this->temp[2]=substr($decitemp,-2); //eg : 28
        return $this->temp; //T° is 26.28°C
    }
}
