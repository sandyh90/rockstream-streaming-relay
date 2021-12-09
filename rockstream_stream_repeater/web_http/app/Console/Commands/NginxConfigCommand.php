<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\StreamInput;

use App\Component\NginxConfigGen;

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
        $check_input = StreamInput::with('ingest_destination_data');

        if ($check_input->count() > 0) {
            # check nginx executable path if not found then exit or else continue
            $nginx_folder = (dirname(base_path()) . DIRECTORY_SEPARATOR . config('component.nginx_path'));
            if (!file_exists($nginx_folder . '\nginx.exe')) {
                return $this->error('Stream input Nginx config cannot be generated, Because nginx not found: ' . $nginx_folder);
            } else {
                # Regenerate Nginx config
                NginxConfigGen::GenerateBaseConfig();

                # check nginx process if running then restart nginx service
                system('QPROCESS * | find /I /N "nginx.exe">NUL', $check_process);

                if ($check_process == 0) {
                    # Reload nginx process to apply changes to config file and restart it
                    system('cmd /c start "" /d"' . $nginx_folder . '" "nginx.exe" -s reload');
                    return $this->info('Stream input nginx config has been generate, Reload success');
                } else {
                    return $this->error('Nginx not running.');
                }
            }
        }
        return $this->error('No stream input found');
    }
}
