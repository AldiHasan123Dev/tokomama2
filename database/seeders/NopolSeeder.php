<?php

namespace Database\Seeders;

use App\Models\Nopol;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NopolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

        $data = [
            [
                'nopol' => 'L 8093 US',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9309 UC',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8418 US',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8421 US',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8816 UR',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8730 UW',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9773 UO',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'H 9134 OF',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8451 UV',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9645 UR (2)',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9462 UC',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9461 UC',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9626 UI',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9625 UI',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9645 UR',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9682 UY',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8734 UV',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9488 UP',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8197 UQ',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'B 9961 SM',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8549 UJ',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 9123 UI',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8652 UM',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8145 UL',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8824 UZ',
                'status' => "aktif",
                'created_at' => now()
            ],
            [
                'nopol' => 'L 8013 UZ',
                'status' => "aktif",
                'created_at' => now()
            ],
        ];

        Nopol::insert($data);
    }
}
