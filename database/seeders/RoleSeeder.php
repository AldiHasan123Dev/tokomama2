<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role')->insert([
            'name' => 'SUPER ADMIN',
            'deleted_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ]);
        
        DB::table('role')->insert([
            'name' => 'ADMIN SJ',
            'deleted_at' => null,
            'created_at' => Carbon::now(),
            'updated_at' => null,
        ]);

        
    }
}
