<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

use App\Component\NginxConfigGen;

class FactoryResetAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rockstream:reset-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Resetting the application...');
        sleep(3);
        $this->info('Dropping all tables...');
        $this->call('migrate:fresh');
        sleep(3);
        $this->info('Clearing optimize...');
        $this->call('optimize:clear');
        sleep(3);
        $this->info('Resetting nginx configuration...');
        try {
            NginxConfigGen::GenerateBaseConfig();
            $this->info('Nginx configuration reset successfully.');
        } catch (\Exception $e) {
            return $this->error('Stream input Nginx config cannot be generated, Error: ' . $e->getMessage());
        }
        if (file_exists(storage_path('app/settings-app.json'))) {
            sleep(3);
            $this->info('Remove applied setting app.');
            unlink(storage_path('app/settings-app.json'));
        }
        sleep(3);
        $this->info('Erase Logs...');
        $files = Arr::where(Storage::disk('log')->files(), function ($filename) {
            return Str::endsWith($filename, '.log');
        });
        $count = count($files);
        if (Storage::disk('log')->delete($files)) {
            $this->info(sprintf('Deleted %s %s!', $count, Str::plural('file', $count)));
        } else {
            $this->error('Error in deleting log files!');
        }
        sleep(3);
        $this->info('Application has been reset successfully.');
    }
}
