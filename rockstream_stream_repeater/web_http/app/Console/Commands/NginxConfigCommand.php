<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Component\NginxConfigGen;

use App\Component\Utility;

class NginxConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nginxrtmp:regenconfig';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate RTMP config in nginx configuration file';

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
            return $this->error('Stream input Nginx config cannot be generated, Because nginx not found: ' . $nginx_folder);
        } else {
            # Regenerate Nginx config
            try {
                NginxConfigGen::GenerateBaseConfig();
            } catch (\Exception $e) {
                return $this->error('Stream input Nginx config cannot be generated, Error: ' . $e->getMessage());
            }

            # check nginx process if running then restart nginx service
            $check_process = Utility::getInstanceRunByPath((Utility::defaultBinDirFolder(config('component.nginx_path')) . DIRECTORY_SEPARATOR . 'nginx.exe'))['found_process'];

            if ($check_process == true) {
                # Reload nginx process to apply changes to config file and restart it
                Utility::runInstancewithPid('cmd /c start /B "" /d"' . $nginx_folder . '" "nginx.exe" -s reload');
                return $this->info('Stream input nginx config has been generate, Reload success');
            } else {
                return $this->error('Stream input nginx config has been generate, But nginx not running.');
            }
        }
    }
}
