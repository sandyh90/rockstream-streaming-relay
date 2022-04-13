<?php

namespace App\Component;

use Illuminate\Support\Str;

use SoftCreatR\MimeDetector\MimeDetector;
use SoftCreatR\MimeDetector\MimeDetectorException;

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

    public static function getInstanceRunByPath($ProgramPath = NULL)
    {
        if (is_null($ProgramPath)) {
            return FALSE;
        } else {
            # Check if program is running or not
            $ProcPath = strtolower($ProgramPath);
            $ResultData = [];
            $procData = NULL;
            $get_data = explode("\r\n", trim(shell_exec('wmic process get ExecutablePath,ProcessId 2>NUL')));
            if (!empty($get_data) && count($get_data) > 0) {
                foreach (array_slice($get_data, 1) as $procData) {
                    $procData = preg_split('/\s+\s+/', $procData, 0, PREG_SPLIT_NO_EMPTY);
                    if (isset($procData[1])) {
                        $pid  = $procData[1];
                        $path = strtolower($procData[0]);

                        $ResultData[$path] = ['pids' => [$pid], 'found_process' => ($path == $ProcPath ? TRUE : FALSE)];
                    }
                }
            }
            if (array_key_exists($ProcPath, $ResultData)) {
                return $ResultData[$ProcPath];
            } else {
                return $ResultData[$ProcPath] = ['pids' => [], 'found_process' => FALSE];
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

    public static function checkFileMime($FileInput = NULL, $FileMimeOrExt = NULL)
    {
        if ($FileMimeOrExt == NULL || $FileInput == NULL || !is_array($FileMimeOrExt)) {
            return false;
        } else {
            $mimeDetector = new MimeDetector();
            try {
                $mimeDetector->setFile($FileInput);
            } catch (MimeDetectorException $e) {
                return false;
            }
            return in_array($mimeDetector->getMimeType(), $FileMimeOrExt);
        }
    }

    public static function defaultBinDirFolder($foldername = NULL)
    {
        if (is_null($foldername)) {
            return false;
        } else {
            return (dirname(base_path()) . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . $foldername);
        }
    }
}
