<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;

class AccountManageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'accountmanage:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the password of the specified user';

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
        $username = $this->ask('What is your username?');
        $password = $this->secret('What password that want to use?');

        if (!$username || !$password) {
            return $this->error('Please enter your username and password.');
        } else {
            $user = User::where('username', $username)->first();

            if (!$user) {
                return $this->error('The username is not exist.');
            } else {
                if (Str::length($password) <= 8) {
                    return $this->error('The password is too short, min 8 character.');
                } else {
                    $user->update(['password' => Hash::make($password)]);
                    return $this->info('Congratulations, Reset account successfuly' . "\n" . 'Username:' . $username . "\n" .
                        'Password: *That you input*');
                }
            }
        }
    }
}
