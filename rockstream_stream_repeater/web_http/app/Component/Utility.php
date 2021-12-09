<?php

namespace App\Component;

class Utility
{

    public static function getInstanceRun($program = NULL)
    {
        if (is_null($program)) {
            return false;
        } else {
            # Check if program is running or not
            system('QPROCESS * | find /I /N "' . $program . '">NUL', $val);
            if ($val == 0) {
                return true;
            } else {
                return false;
            }
        }
    }
}
