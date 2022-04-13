<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\StreamInput;

use App\Component\Utility;

class NginxPowerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nginxrtmp:power';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Power control Nginx process';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        # check nginx executable path if not found then exit or else continue
        $nginx_folder = Utility::defaultBinDirFolder(config('component.nginx_path'));
        if (!file_exists($nginx_folder . '\nginx.exe')) {
            return $this->error('Nginx not found: ' . $nginx_folder);
        } else {
            # check nginx process if running then restart nginx service
            $check_process = Utility::getInstanceRunByPath((Utility::defaultBinDirFolder(config('component.nginx_path')) . DIRECTORY_SEPARATOR . 'nginx.exe'))['found_process'];

            if ($check_process == true) {
                # Set is live in database to false / offline if nginx process is turn off
                $stream_db = StreamInput::where(['is_live' => TRUE]);
                if ($stream_db->exists()) {
                    $stream_db->update(['is_live' => FALSE]);
                }

                # Reload nginx process to apply changes to config file and restart it
                Utility::runInstancewithPid('cmd /c start /B "" /d"' . $nginx_folder . '" "nginx.exe" -s stop');
                return $this->info('Power Off Nginx successfully');
            } else {
                # Turn on nginx process if it is off
                Utility::runInstancewithPid('cmd /c start /B "" /d"' . $nginx_folder . '" "nginx.exe"');
                return $this->info('Power On Nginx successfully.');
            }
        }
    }
}
