<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['title'=>'Master','name'=>'master','icon'=>'fa-database','url'=>'#','order'=>1],
            ['title'=>'Surat Jalan','name'=>'surat-jalan','icon'=>'fa-person-walking-luggage','url'=>'#','order'=>2],
            ['title'=>'Keuangan','name'=>'keuangan','icon'=>'fa-money-check-dollar','url'=>'#','order'=>3],
            ['title'=>'Pajak','name'=>'pajak','icon'=>'fa-circle-dollar-to-slot','url'=>'#','order'=>4],
            ['title'=>'Jurnal Keuangan','name'=>'jurnal-keuangan','icon'=>'fa-book','url'=>'#','order'=>5],
        ];

        Menu::insert($data);
    }
}
