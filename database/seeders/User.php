<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('users')->insert([
            'fname' => 'Rong',
            'lname' => 'Marin',
            'username' => 'SakKaRin',
            'email' => 'sakkarin@gmail.com',
            'password' => Hash::make('$admin168$'),
            'type' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
       ]);
    }
}
