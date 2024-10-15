<?php

namespace Database\Seeders;

use App\Models\RoleMenu;
use App\Models\SubMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['menu_id'=>1,'title'=>'Customer','name'=>'customer','icon'=>'#','url'=>'/master/customer','order'=>1],
            ['menu_id'=>1,'title'=>'Barang','name'=>'barang','icon'=>'#','url'=>'/master/barang','order'=>2],
            ['menu_id'=>1,'title'=>'Nopol','name'=>'nopol','icon'=>'#','url'=>'/master/nopol','order'=>3],
            ['menu_id'=>1,'title'=>'Ekspedisi','name'=>'ekspedisi','icon'=>'#','url'=>'/master/ekspedisi','order'=>4],
            ['menu_id'=>1,'title'=>'Satuan','name'=>'satuan','icon'=>'#','url'=>'/master/satuan','order'=>5],
            ['menu_id'=>1,'title'=>'User','name'=>'user','icon'=>'#','url'=>'/master/user','order'=>6],
            ['menu_id'=>1,'title'=>'Role Menu','name'=>'role-menu','icon'=>'#','url'=>'/master/role-menu','order'=>7],
            ['menu_id'=>1,'title'=>'Supplier','name'=>'supplier','icon'=>'#','url'=>'/master/supplier','order'=>8],
            ['menu_id'=>2,'title'=>'Buat Surat Jalan (SJ)','name'=>'surat-jalan-create','icon'=>'#','url'=>'/surat-jalan/create','order'=>1],
            ['menu_id'=>2,'title'=>'List Surat Jalan (SJ)','name'=>'surat-jalan-index','icon'=>'#','url'=>'/surat-jalan','order'=>2],
            ['menu_id'=>2,'title'=>'Harga Beli & Jual','name'=>'harga-barang','icon'=>'#','url'=>'/surat-jalan-tarif-barang','order'=>3],
            ['menu_id'=>3,'title'=>'Pre Invoice','name'=>'pre-invoice','icon'=>'#','url'=>'/keuangan/pre-invoice','order'=>1],
            ['menu_id'=>3,'title'=>'Invoice','name'=>'invoice','icon'=>'#','url'=>'/keuangan/invoice','order'=>2],
            ['menu_id'=>3,'title'=>'Laporan Omzet','name'=>'omzet','icon'=>'#','url'=>'/keuangan/omzet','order'=>3],
            ['menu_id'=>4,'title'=>'Nomor Seri (NSFP)','name'=>'nsfp','icon'=>'#','url'=>'/pajak/nsfp','order'=>1],
            ['menu_id'=>4,'title'=>'Laporan PPN','name'=>'laporan-ppn','icon'=>'#','url'=>'/pajak/laporan-ppn','order'=>2],
            ['menu_id'=>5,'title'=>'COA','name'=>'coa','icon'=>'#','url'=>'/coa','order'=>1],
            ['menu_id'=>5,'title'=>'Jurnal','name'=>'jurnal','icon'=>'#','url'=>'/jurnal','order'=>2],
            ['menu_id'=>5,'title'=>'Template Jurnal','name'=>'jurnal-template','icon'=>'#','url'=>'/template-jurnal','order'=>3],
            ['menu_id'=>5,'title'=>'Laporan Neraca','name'=>'jurnal-neraca','icon'=>'#','url'=>'#','order'=>4],
            ['menu_id'=>5,'title'=>'Laporan Laba/Rugi','name'=>'jurnal-lr','icon'=>'#','url'=>'#','order'=>5],
            ['menu_id'=>5,'title'=>'Laporan Buku Besar','name'=>'jurnal-buku-besar','icon'=>'#','url'=>'#','order'=>6],
            ['menu_id'=>5,'title'=>'Laporan Buku Besar Pembantu','name'=>'jurnal-buku-pembantu','icon'=>'#','url'=>'#','order'=>7],
        ];

        SubMenu::insert($data);

        for ($i=1; $i <= 21; $i++) {
            RoleMenu::create([
                'role_id' => 1,
                'menu_id' => $i
            ]);
        }
    }
}
