<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// database/seeders/DatabaseSeeder.php
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
        ]);
    }
}
