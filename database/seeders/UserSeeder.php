<?php

namespace Database\Seeders;

use App\Entities\Key;
use App\Entities\Status;
use App\Entities\UserRoles;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::firstOrCreate([
            'name' => 'admin',
            'email' => 'admin@elhesn.com',
            'password' => Hash::make('password'),
            'role' => UserRoles::ADMIN,
            'status' => Status::ACTIVE
        ]);

    }
}
