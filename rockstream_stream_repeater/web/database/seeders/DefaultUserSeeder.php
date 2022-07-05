<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('12345678'),
            'is_operator' => TRUE,
            'is_active' => TRUE,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
