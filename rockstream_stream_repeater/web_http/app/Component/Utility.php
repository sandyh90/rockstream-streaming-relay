<?php

namespace App\Component;

class Utility
{

    public static function getInstanceRun($ProgramOrPid = NULL)
    {
        if (is_null($ProgramOrPid)) {
            return false;
        } else {
            # Check if program is running or not
            system('QPROCESS * | find /I /N "' . $ProgramOrPid . '">NUL', $val);
            if ($val == 0) {
                return true;
            } else {
                return false;
            }
        }
    }

    public static function runInstancewithPid($command = NULL)
    {
        if (is_null($command)) {
            return false;
        } else {
            $descriptorspec = [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"]
            ];
            $process = proc_open($command, $descriptorspec, $pipes);
            $output = proc_get_status($process);
            proc_close($process);
            return $output;
        }
    }

    public static function getreadableBit($bytes = 0)
    {
        if ($bytes < 1024) {
            return $bytes . 'b/s';
        } else {
            $i = floor(log($bytes) / log(1024));
            $sizes = array('b/s', 'Kb/s', 'Mb/s', 'Gb/s', 'Tb/s', 'Pb/s', 'Eb/s', 'Zb/s', 'Yb/s');

            return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
        }
    }

    public static function getreadableBytes($bytes = 0)
    {
        if ($bytes < 1024) {
            return $bytes . 'B';
        } else {
            $i = floor(log($bytes) / log(1024));
            $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

            return sprintf('%.02F', $bytes / pow(1024, $i)) * 1 . ' ' . $sizes[$i];
        }
    }
}
