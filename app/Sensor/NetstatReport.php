<?php

namespace App\Sensor;

/**
 * Description of NetstatReport
 *
 * @author tibo
 */
class NetstatReport
{
    public $time = 0;
    public $tcp_segments_sent = 0;
    public $tcp_segments_retransmitted = 0;
    
    const TCP_SENT = '/^    (\d+) segments sent out/m';
    const TCP_RETRANSMITTED = '/^    (\d+) segments retransmitted$/m';
    
    public static function parse(string $string) : NetstatReport
    {
        $report = new NetstatReport;
        $report->tcp_segments_retransmitted =
                self::pregMatchOne(self::TCP_RETRANSMITTED, $string, 0);
        $report->tcp_segments_sent =
                self::pregMatchOne(self::TCP_SENT, $string, 0);

        return $report;
    }

    public static function pregMatchOne($pattern, $string, $default = null)
    {
        $matches = [];
        if (preg_match($pattern, $string, $matches) === 1) {
            return $matches[1];
        }

        return $default;
    }
}
