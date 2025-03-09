<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// database/seeders/UsersTableSeeder.php
class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Production Managers
        $pmRole = Role::where('name', 'Production Manager')->first();
        User::create([
            'name' => 'Manager',
            'email' => 'pm@example.com',
            'password' => bcrypt('password'),
            'role_id' => $pmRole->id
        ]);
        

        // Operators
        $operatorRole = Role::where('name', 'Operator')->first();
        User::create([
            'name' => 'Operator 1',
            'email' => 'op1@example.com',
            'password' => bcrypt('password'),
            'role_id' => $operatorRole->id
        ]);

        $operatorRole = Role::where('name', 'Operator')->first();
        User::create([
            'name' => 'Operator 2',
            'email' => 'op2@example.com',
            'password' => bcrypt('password'),
            'role_id' => $operatorRole->id
        ]);
    }
}
