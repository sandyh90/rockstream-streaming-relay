<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Component\Utility;
use App\Component\Facades\Facade\AppInterfacesFacade as AppInterfaces;

class NginxReloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nginxrtmp:restart';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restart Nginx';

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
        $binaryProc = [
            'nginxBinName' => 'nginx.exe',
            'nginxPath' => ((AppInterfaces::getsetting('IS_CUSTOM_NGINX_BINARY') == TRUE && !empty(AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY'))) ? AppInterfaces::getsetting('NGINX_BINARY_DIRECTORY') : Utility::defaultBinDirFolder('nginx'))
        ];

        # check nginx executable path if not found then exit or else continue
        if (!file_exists($binaryProc['nginxPath'] . DIRECTORY_SEPARATOR . $binaryProc['nginxBinName'])) {
            return $this->error('Nginx not found: ' . $binaryProc['nginxPath']);
        } else {
            # check nginx process if running then restart nginx service
            if (Utility::getInstanceRunByPath($binaryProc['nginxPath'] . DIRECTORY_SEPARATOR . $binaryProc['nginxBinName'], $binaryProc['nginxBinName'])['found_process']) {
                # Reload nginx process to apply changes to config file and restart it
                try {
                    Utility::runInstancewithPid('cmd /c start /B "" /d"' . $binaryProc['nginxPath'] . '" "' . $binaryProc['nginxBinName'] . '" -s reload');
                    return $this->info('Restart Nginx successfully');
                } catch (\Throwable $e) {
                    return $this->error('Restart Nginx unsuccessfully, Error:' . $e->getMessage());
                }
            } else {
                return $this->error('Nginx not running.');
            }
        }
    }
}
