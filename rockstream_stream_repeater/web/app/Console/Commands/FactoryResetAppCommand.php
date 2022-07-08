<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        # Regenerate Nginx config
        try {
            NginxConfigGen::GenerateBaseConfig();
        } catch (\Exception $e) {
            return $this->error('Stream input Nginx config cannot be generated, Error: ' . $e->getMessage());
        }
        sleep(3);
        $this->info('Application has been reset successfully.');
    }
}
