<?php

namespace App\Sensor;

/**
 * Description of MacVendor
 *
 * @author tibo
 */
class MacVendor
{

    /**
     * Return the vendor name that owns the supplied MAC address.
     *
     * The CSV file must be in the format that you showed:
     *   Mac Prefix,Vendor Name,Private,Block Type,Last Update
     *   00:00:0C,"Cisco Systems, Inc",false,MA-L,2015/11/17
     *
     * @param string $mac        The full MAC address (any separator, any case).
     * @return string|null       Vendor name on success, null if not found or file error.
     */
    public function lookup(string $mac): ?string
    {
        // ------------------------------------------------------------------
        // 1. Normalise the supplied MAC to the same style that appears in
        //    the CSV file (uppercase, colon‑separated).
        // ------------------------------------------------------------------
        $cleanMac = strtoupper(str_replace(['-', '.'], ':', $mac));
        
        // Keep only the first three octets → “XX:XX:XX”
        $prefix = substr($cleanMac, 0, 8);     // 8 chars = 2*3 + 2 colons
        
        $this->loadFile();
        
        return self::$cache[$prefix] ?? null;
    }
    
    private static $cache = null;
    
    private function loadFile()
    {
        $csvFile = __DIR__ . "/MacVendors.csv";
                
        
        if (!is_readable($csvFile)) {
            // File not found or not readable.
            self::$cache = [];
        }
        
        // ------------------------------------------------------------------
        // 2. Open the CSV file.  We use the built‑in CSV reader so that
        //    quoted fields (like “Cisco Systems, Inc”) are handled correctly.
        // ------------------------------------------------------------------
        if (self::$cache === null) {
            // Load the whole file into an associative array: prefix → vendor.
            self::$cache = [];
            if (($fh = fopen($csvFile, 'r')) !== false) {
                // Skip the header
                fgetcsv($fh);
                while (($row = fgetcsv($fh)) !== false) {
                    // $row[0] = Mac Prefix, $row[1] = Vendor Name
                    self::$cache[trim(strtoupper($row[0]))] = $row[1];
                }
                fclose($fh);
            }
        }
    }
}
