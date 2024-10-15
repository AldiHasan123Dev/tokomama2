<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'role_id' => 1,
            'name' => "Nanda",
            'email' => "nanda@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make('rahasia123'),
            'phone' => "085812345678",
            'address' => "SBY",
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => null,
            'deleted_at' => null
        ]);

        DB::table('users')->insert([
            'role_id' => 1,
            'name' => "Galeh",
            'email' => "galeh@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make('rahasia123'),
            'phone' => "085812345678",
            'address' => "SDJ",
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => null,
            'deleted_at' => null
        ]);

        DB::table('users')->insert([
            'role_id' => 1,
            'name' => "Trial",
            'email' => "trial@gmail.com",
            'email_verified_at' => now(),
            'password' => Hash::make('cobacoba'),
            'phone' => "085812345678",
            'address' => "SDJ",
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => null,
            'deleted_at' => null
        ]);
    }
}
