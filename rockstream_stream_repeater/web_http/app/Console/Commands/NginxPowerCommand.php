<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $nginx_folder = (dirname(base_path()) . DIRECTORY_SEPARATOR . config('component.nginx_path'));
        if (!file_exists($nginx_folder . '\nginx.exe')) {
            return $this->error('Nginx not found: ' . $nginx_folder);
        } else {
            # check nginx process if running then restart nginx service
            system('QPROCESS * | find /I /N "nginx.exe">NUL', $check_process);

            if ($check_process == 0) {
                # Reload nginx process to apply changes to config file and restart it
                system('taskkill /f /IM nginx.exe >NUL 2>NUL');
                return $this->info('Power Off Nginx successfully');
            } else {
                system('cmd /c start "" /d"' . $nginx_folder . '" "nginx.exe"');
                return $this->info('Power On Nginx successfully.');
            }
        }
    }
}
