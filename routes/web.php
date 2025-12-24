<?php

use App\Http\Controllers\Api\NSFPController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\BukuBesarPembantuController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerAbController;
use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceExternalController;
use App\Http\Controllers\HargaController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\JurnalManualController;
use App\Http\Controllers\NSFPController as nsfp;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LabaRugi;
use App\Http\Controllers\StockController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AlatBeratController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\Neraca;
use App\Http\Controllers\NopolController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\TemplateJurnalController;
use App\Http\Controllers\DirectSaleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Resources\DatatableResource;
use App\Http\Resources\SuratJalanResource;
use App\Models\Customer;
use App\Models\Jurnal;
use App\Models\NSFP as ModelsNSFP;
use App\Models\SuratJalan;
use App\Models\TemplateJurnal;
use App\Models\TemplateJurnalItem;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect('login');
});

Route::get('/server-time', function () {
    return response()->json(['time' => now()->format('Y-m-d H:i:s')]);
})->name('server.time');
// Route::get('test', function () {
//     $data1 = SuratJalan::get();
//     $data = SuratJalanResource::collection($data1);
//     $res = $data->toArray(request());
//     return response($data);
// });
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/surat-jalan/checkBarangCount', [SuratJalanController::class, 'checkBarangCount'])->name('surat-jalan.checkBarangCount');

Route::middleware('auth')->group(function () {
    Route::get('/get-stock/{id}', [SuratJalanController::class, 'getStock'])->name('sj.getStock');
    Route::get('/get-harga', [DirectSaleController::class, 'getHarga']);
    Route::post('/master/blokir',  [CustomerController::class, 'blokir'])->name('master.customer.blokir.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/master/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/barang-masuk/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/laporan/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/keuangan/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/pajak/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/surat-jalan/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/direct_sale/profile', [ProfileController::class, 'edit'])->name('profile.edit');


    Route::put('/profileUpdate/{id}', [ProfileController::class, 'update1'])->name('profile.update1');
    Route::get('jurnal-code', [JurnalController::class, 'code'])->name('jurnal.code');
    Route::get('/jurnal-balik/cari', [JurnalController::class, 'JurnalBalikcari'])
    ->name('jurnal-balik.cari');
    Route::get('jurnal-balik', [JurnalController::class, 'balik'])->name('jurnal.balik');
    Route::get('/master/blokir-customer', [CustomerController::class, 'blokir_cust'])->name('blokir.cust');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/barang-masuk/monitor-stock', [StockController::class, 'monitor_stock'])->name('monitor-stock');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/jurnal/kode/list', [JurnalController::class, 'listKodeJurnal'])
    ->name('jurnal.kode.list');
    Route::post('jurnal-balik/proses', [JurnalController::class, 'prosesJurnalBalik'])
    ->name('jurnal-balik.proses');
    Route::post('/jurnal/simpan-kode', [JurnalController::class, 'simpanKode'])->name('jurnal.simpanKode');
    Route::get('/surat-jalan-cetak/{surat_jalan}', [SuratJalanController::class, 'cetak'])->name('surat-jalan.cetak');
    Route::get('/keuangan/invoice-pending/cetak', [KeuanganController::class, 'cetakDraftInv'])->name('draft-invoice.cetak');
    Route::get('/surat-jalan-tarif-barang', [SuratJalanController::class, 'tarif'])->name('surat-jalan.barang');
    Route::get('/barang-masuk/harga-beli', [SuratJalanController::class, 'harga_beli'])->name('harga_beli');
    Route::get('/surat-jalan/editBarang', [SuratJalanController::class, 'editBarang'])->name('surat-jalan.editBarang');
    Route::post('/surat-jalan/editBarang', [SuratJalanController::class, 'editBarangPost'])->name('surat-jalan.editBarang');
    Route::delete('/surat-jalan/hapusBarang/', [SuratJalanController::class, 'hapusBarang'])->name('surat-jalan.hapusBarang');
    Route::post('/surat-jalan/tambahBarang', [SuratJalanController::class, 'tambahBarang'])->name('surat-jalan.tambahBarang');
    Route::post('/surat-jalan-data', [SuratJalanController::class, 'dataTable'])->name('surat-jalan.data');
    Route::post('/surat-jalan-supplier-data', [SuratJalanController::class, 'dataTableSupplier'])->name('surat-jalan-supplier.data');
    Route::post('/surat-jalan-edit', [SuratJalanController::class, 'update'])->name('surat-jalan.data.edit');
    Route::post('/update-tipe-jurnal-bayar-inv', [KeuanganController::class, 'updateTipeJurnal']);
    Route::post('/surat-jalan-external-edit', [SuratJalanController::class, 'updateInvoiceExternal'])->name('surat-jalan-external.data.edit');
    Route::post('/surat-jalan-delete', [SuratJalanController::class, 'destroy'])->name('surat-jalan.data.delete');
    // Route::delete('/surat-jalan-delete', [SuratJalanController::class, 'destroy'])->name('surat-jalan.data.delete');
    Route::get('/barang-masuk/create', [SuratJalanController::class, 'create_bm'])->name('barang_masuk');
    Route::get('/surat-jalan/create_noppn', [SuratJalanController::class, 'createNoPPN'])->name('surat-jalan.create_noppn');
    Route::get('/barang-masuk/cetak-nota', [StockController::class, 'cetak_nota'])->name('cetak_nota');
    Route::post('/barang-masuk/store', [SuratJalanController::class, 'store_bm'])->name('barang_masuk.store');
    Route::resource('surat-jalan', SuratJalanController::class);
    Route::resource('invoice-transaksi', InvoiceController::class);
    Route::post('/mob/hapus', [KeuanganController::class, 'hpsMob'])->name('mob.destroy');
    Route::match(['get', 'post'], '/preview-invoice', [InvoiceController::class, 'preview'])->name('preview.invoice');
    Route::match(['get', 'post'], '/keuangan/invoice-pending/pre-draft-invoice', [KeuanganController::class, 'previewDraftInv'])->name('pending.invoice.preview_DraftInv');
    Route::get('/keuangan/form-inv-ab', [KeuanganController::class, 'formInvoiceAb'])->name('invoice-ab.form');
    Route::resource('jurnal', JurnalController::class);
    Route::get('/stock/cetak/{id}', [StockController::class, 'cetak'])->name('stock.cetak');
    Route::get('/select2/tarif-ab', [KeuanganController::class, 'tarifAlatBerat'])
    ->name('tarif-ab.select2');
    Route::get('/select2/customer', [KeuanganController::class, 'customer'])
        ->name('customer.select2');
    Route::post('/order-alat-berat/store', [KeuanganController::class, 'simpanOrder'])
    ->name('order-ab.store');
    Route::get('/mob/by-order', [KeuanganController::class, 'mobByOrder'])
    ->name('mob.by-order');
    Route::post('/tambah-tagihan', [KeuanganController::class, 'tambahTagihan'])->name('tambah-tagihan');
    Route::post('/update-lock/{id}', [StockController::class, 'updateLock']);
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/data-jurnal', [JurnalController::class, 'dataJurnal'])->name('jurnal.data');
    Route::post('/jurnal-export', [JurnalController::class, 'exportJurnal'])->name('jurnal.export');
    Route::get('/jurnal-edit', [JurnalController::class, 'edit'])->name('jurnal.edit');
    Route::get('/jurnal-merger', [JurnalController::class, 'merger'])->name('jurnal.jurnal-merger');
    Route::post('/jurnal-merger', [JurnalController::class, 'merger_store'])->name('jurnal.jurnal-merger');
    Route::post('/jurnal-store', [JurnalController::class, 'store'])->name('jurnal.edit.store');
    Route::post('/jurnal-update', [JurnalController::class, 'update'])->name('jurnal.edit.update');
    Route::post('/jurnal-delete', [JurnalController::class, 'destroy'])->name('jurnal.item.delete');
    Route::post('/jurnal-tgl-update', [JurnalController::class, 'tglUpdate'])->name('jurnal.edit.tglupdate');
    Route::get('/jurnal-edit-list', [JurnalController::class, 'datatableEdit'])->name('jurnal.edit.list');
    Route::get('/jurnal-transaksi', [JurnalManualController::class, 'transaksi'])->name('jurnal-manual-transaksi');
    Route::get('/lap-qty', [StockController::class, 'lapQty'])->name('lap.qty');
    Route::get('/data-lap-qty', [StockController::class, 'qty'])->name('data.qty');
    Route::resource('jurnal-manual', JurnalManualController::class);
    Route::post('jurnal-hutang', [JurnalManualController::class, 'Jurnalhutang'])->name('jurnal.hutang');
    Route::post('/jurnal-manual-template', [JurnalManualController::class, 'terapanTemplateJurnal'])->name('jurnal.template.terapan');
    Route::post('jurnal-sj-wherejob', [JurnalManualController::class, 'getInvoiceWhereNoInv'])->name('jurnal.sj.whereInv');
    Route::post('jurnal-sj-whereinvext', [JurnalManualController::class, 'getInvoiceWhereNoInvExt'])->name('jurnal.sj.whereInvExt');
    Route::post('ekspedisi-data', [EkspedisiController::class, 'dataTable'])->name('ekspedisi.data');
    Route::post('transaction-data', [TransactionController::class, 'dataTable'])->name('transaksi.data');
    Route::post('transaction-data1', [TransactionController::class, 'dataTable1'])->name('transaksi.data1');
    Route::put('transaction-update', [TransactionController::class, 'update'])->name('transaksi.update');
    Route::put('transaction-update1', [TransactionController::class, 'update1'])->name('transaksi.update1');
    // Route::get('coa', [CoaController::class,'index'])->name('jurnal.coa');
    // Route::post('coa', [CoaController::class,'statusCoa'])->name('jurnal.coa');
    Route::get('coa', [CoaController::class,'index'])->name('jurnal.coa');
    Route::post('coa', [CoaController::class,'store'])->name('jurnal.coa.store');
    Route::put('coa/{coa}', [CoaController::class,'update'])->name('jurnal.coa.update');
    Route::delete('coa/{coa}', [CoaController::class,'destroy'])->name('jurnal.coa.destroy');
    Route::get('coa/data', [CoaController::class, 'dataTable'])->name('jurnal.coa.data');

    Route::post('coa', [CoaController::class,'store'])->name('jurnal.coa.store');
    Route::put('coa/{coa}', [CoaController::class,'update'])->name('jurnal.coa.update');
    Route::delete('coa/{coa}', [CoaController::class,'destroy'])->name('jurnal.coa.destroy');
    Route::get('coa/data', [CoaController::class, 'dataTable'])->name('jurnal.coa.data');
    Route::post('/data/update-aktif', [HargaController::class, 'updateAktif'])->name('data.updateAktif');
    Route::post('/data/update-non-aktif', [HargaController::class, 'updateNonAktif'])->name('data.updateNonAktif');
    Route::post('/data/update-tarif-aktif', [TarifController::class, 'updateTarifAktif'])->name('data.updateTarifAktif');
    Route::post('/data/update-tarif-non-aktif', [TarifController::class, 'updateTarifNonAktif'])->name('data.updateTarifNonAktif');

    
    Route::post('/jurnal/coa/store', [CoaController::class, 'store'])->name('jurnal.coa.store');
    Route::post('coa-delete', [CoaController::class,'hapusCoa'])->name('jurnal.coa.delete');

    Route::post('/jurnal/coa/store', [CoaController::class, 'store'])->name('jurnal.coa.store');
    Route::get('template-jurnal', [TemplateJurnalController::class,'index'])->name('jurnal.template-jurnal');
    Route::get('template-jurnal-list', [TemplateJurnalController::class,'datatable'])->name('jurnal.template-jurnal.data');
    Route::get('template-jurnal-create', [TemplateJurnalController::class,'create'])->name('jurnal.template-jurnal.create');
    Route::post('template-jurnal-edit', [TemplateJurnalController::class,'edit'])->name('jurnal.template-jurnal.edit');
    // Route::get('template-jurnal-editView', [TemplateJurnalController::class,'edit'])->name('jurnal.template-jurnal.editView');
    Route::post('template-jurnal-add', [TemplateJurnalController::class,'store'])->name('jurnal.template-jurnal.add');
    Route::post('template-jurnal-update', [TemplateJurnalController::class,'update'])->name('jurnal.template-jurnal.update');
    Route::post('template-jurnal-delete', [TemplateJurnalController::class,'destroy'])->name('jurnal.template-jurnal.delete');
    Route::post('/omzet-data', [KeuanganController::class, 'dataTableOmzet'])->name('keuangan.omzet.data');
    Route::resource('buku-besar', BukuBesarController::class);
    Route::get('/export/buku-besar', [BukuBesarController::class, 'export'])->name('buku-besar.export');
    Route::get('buku-besar/{month}/{year}', [BukuBesarController::class, 'datatableDefault'])->name('buku-besar.dataf');
    Route::get('bb-data/{month}/{year}/{coa}', [BukuBesarController::class, 'datatable'])->name('buku-besar.data');
    Route::resource('neraca', Neraca::class);
    Route::resource('laba-rugi', LabaRugi::class);
    Route::resource('buku-besar-pembantu', BukuBesarPembantuController::class);
    Route::get('lr-akumulatif', [LabaRugi::class, 'LRberjalan'])->name('lr.berjalan');
    Route::get('buku-besar-pembantu/{id}/detail', [BukuBesarPembantuController::class, 'showDetail'])->name('buku-besar-pembantu.showDetail');
    Route::get('/export-ncs', [BukuBesarPembantuController::class, 'exportNcs'])->name('export.ncs');
    Route::get('/export-customers', [BukuBesarPembantuController::class, 'exportCustomer'])->name('export.customers');
    Route::get('/export-supplier', [BukuBesarPembantuController::class, 'exportSupplier'])->name('export.supplier');
    Route::resource('invoice-external', InvoiceExternalController::class);
});

Route::prefix('keuangan')->controller(KeuanganController::class)->middleware('auth')->group(function () {
    Route::get('', 'index')->name('keuangan');
    Route::get('invoice-pending', 'pendingInvoice')->name('pending.invoice');
    Route::get('invoice-pending/harga-jual', 'inputHargaJual')->name('pending.invoice.harga_jual');
    Route::get('surat-jalan', 'suratJalan')->name('keuangan.surat-jalan');
    Route::post('surat-jalan', 'suratJalanStore')->name('keuangan.surat-jalan');
    Route::post('invoice-pending/cetak-draft-invoice', 'storeDraftInv')->name('pending.invoice.draft_invoice.cetak');
    Route::get('invoice', 'invoice')->name('keuangan.invoice');
    Route::get('pre-invoice', 'preInvoice')->name('keuangan.pre-invoice');
    Route::get('pre-invoice-ab', 'preInvoiceAb')->name('keuangan.pre-invoice-ab');
    Route::get('jurnal-bayar', 'jurnalBayar')->name('keuangan.jurnal-bayar');
    Route::post('draf-invoice/{surat_jalan}', 'submitInvoice')->name('keuangan.invoice.submit');
    Route::get('draf-invoice/{surat_jalan}', 'invoiceDraf')->name('keuangan.invoice.draf');
    Route::get('cetak-invoice', 'cetakInvoice')->name('keuangan.invoice.cetak');
    Route::get('cetak-invoicesp', 'cetakInvoicesp')->name('keuangan.invoicesp.cetak');
    Route::get('omzet', 'omzet')->name('keuangan.omzet');
    Route::get('omzet-total', 'omzet_total')->name('keuangan.omzet-total');
    Route::get('data-omzet-total', 'dataOmzeTotal')->name('keuangan.data-omzet-total');
    Route::get('omzet-list', 'dataTableOmzet')->name('keuangan.omzet.datatable');
    Route::post('omzet-export', 'OmzetExportExcel')->name('keuangan.omzet.exportexcel');
    Route::post('cari-transaksi-by-tanggal', 'cari')->name('keuangan.cari');
    Route::post('cari-transaksi-by-tanggal1', 'cari1')->name('keuangan.cari1');  
    Route::post('jurnal-simpan', 'jurnal')->name('keuangan.jurnal-inv'); 
});

Route::prefix('toko')->controller(StockController::class)->middleware('auth')->group(function () {
    Route::get('/stock-csv', 'stockCSV')->name('stock.csv');
    Route::get('/stock-csv19', 'stockCSV19')->name('stock19.csv');
    Route::get('/stock', 'stocks')->name('stock'); 
    Route::post('/stock-update', 'update_stock')->name('stock.update_stock');
    Route::get('/stock-{id}-edit', 'edit_stock')->name('stock.edit_stock');
    Route::get('data-barang','getdataBarangOptions')->name('stock.barang-options');
    Route::get('/cetak-nota/{no_bm}', [StockController::class, 'cetak'])->where('no_bm', '.*') // Menangkap semua karakter termasuk slash
    ->name('cetak_nota.cetak');
    Route::post('stock-barang_masuk', 'barang_masuk')->name('stock.barang_masuk'); 
    Route::get('data-stock', 'dataStock')->name('stock.data');
    Route::get('data-stock1', 'dataStock1')->name('stock.data1');
    Route::get('data-stock2', 'dataStock2')->name('stock.data2');
});

Route::prefix('pajak')->middleware('auth')->group(function () {
    Route::get('nsfp', [PajakController::class, 'index'])->name('pajak.nsfp');
    Route::get('laporan-ppn', [PajakController::class, 'lapPpn'])->name('pajak.laporan-ppn');
    Route::get('laporan-ppn-data', [PajakController::class, 'datatable'])->name('pajak.laporan-ppn.data');
    Route::post('export-laporan-ppn-excel', [PajakController::class, 'PPNExportExcel'])->name('pajak.export.ppnexc');
    Route::post('export-laporan-ppn-csv', [PajakController::class, 'PPNExportCsv'])->name('pajak.export.ppncsv');
});

Route::prefix('master')->controller(CustomerController::class)->middleware('auth')->group(function () {
    Route::get('customer', 'index')->name('master.customer');
    Route::get('role-menu', [MenuController::class,'index'])->name('menu.index');
    Route::get('customer_list', 'datatable')->name('master.customer.list');
    Route::post('customer', 'store')->name('master.customer.add');
    Route::post('customer_delete', 'destroy')->name('master.customer.delete');
    Route::post('costumer_edit', 'update')->name('master.customer.edit');
    Route::resource('ekspedisi', EkspedisiController::class)->only(['index','store','update','destroy']);
});

Route::prefix('master')->controller(CustomerAbController::class)->middleware('auth')->group(function () {
    Route::get('customer_ab', 'index')->name('master.customer_ab');
    Route::get('customer_ab_list', 'datatable')->name('master.customer_ab.list');
    Route::post('customer_ab', 'store')->name('master.customer_ab.add');
    Route::post('customer_ab_delete', 'destroy')->name('master.customer_ab.delete');
    Route::post('costumer_ab_edit', 'update')->name('master.customer_ab.edit');
});

Route::prefix('master')->controller(BarangController::class)->middleware('auth')->group(function () {
    Route::get('barang', 'index')->name('master.barang');
    Route::get('barang_list', 'datatable')->name('master.barang.list');
    Route::post('barang_add', 'store')->name('master.barang.add');
    Route::post('barang_edit', 'update')->name('master.barang.edit');
    Route::post('barang_delete', 'destroy')->name('master.barang.delete');
});

Route::prefix('master')->controller(AlatBeratController::class)->middleware('auth')->group(function () {
    Route::get('alat-berat', 'index')->name('master.alat_berat');
    Route::get('alat-berat_list', 'datatable')->name('master.alat_berat.list');
    Route::post('alat-berat_add', 'store')->name('master.alat_berat.add');
    Route::post('alat-berat_edit', 'update')->name('master.alat_berat.edit');
    Route::post('alat-berat_delete', 'destroy')->name('master.alat_berat.delete');
});

Route::prefix('master')->controller(TarifController::class)->middleware('auth')->group(function () {
    Route::get('tarif', 'index')->name('master.tarif');
    Route::get('tarif_list', 'Tarifjqgrid')->name('master.tarif.list');
    Route::post('tarif_add', 'store')->name('master.tarif.add');
    Route::post('tarif_edit', 'update')->name('master.tarif.edit');
    Route::get('harga-data-nonaktif', 'Hargajqgrid')->name('harga.nonaktif.data');
    Route::get('harga-data-aktif', 'Hargajqgrid1')->name('harga.aktif.data');
    Route::post('tarif_delete', 'destroy')->name('master.tarif.delete');
});

Route::prefix('master')->controller(NopolController::class)->middleware('auth')->group(function () {
    Route::get('nopol', 'index')->name('master.nopol');
    Route::get('nopol_list', 'datatable')->name('master.nopol.list');
    Route::post('nopol_add', 'store')->name('master.nopol.add');
    Route::post('nopol_edit', 'update')->name('master.nopol.edit');
    Route::post('nopol_delete', 'destroy')->name('master.nopol.delete');
    Route::post('set_status', 'setStatus')->name('master.nopol.editstatus');
});

Route::prefix('direct_sale')->controller(DirectSaleController::class)->middleware('auth')->group(function () {
    Route::get('ppn', 'ds_ppn')->name('ds.ppn');
    Route::get('non_ppn', 'ds_nonppn')->name('ds.non-ppn');
    Route::post('ppn-tambah', 'ppn_store')->name('ds.ppn-store');
    Route::post('ds-invoice', 'dsInv_store')->name('invoice-ds.store');
});

Route::prefix('master')->controller(HargaController::class)->middleware('auth')->group(function () {
    Route::get('harga', 'index')->name('master.harga');
    Route::post('harga-tambah', 'store')->name('master.harga.tambah');
    Route::get('harga-data-nonaktif', 'Hargajqgrid')->name('harga.nonaktif.data');
    Route::get('harga-data-aktif', 'Hargajqgrid1')->name('harga.aktif.data');
});

Route::prefix('master')->controller(UserController::class)->middleware('auth')->group(function () {
    Route::resource('user', UserController::class)->only(['index','store','update','destroy']);
    ROute::get('data_user_with_role', 'datatable')->name('master.user.data');
});

Route::prefix('master')->controller(SatuanController::class)->middleware('auth')->group(function () {
    Route::resource('satuan', SatuanController::class)->only(['index','store','update','destroy']);
    Route::get('stauan-data', 'dataTable')->name('master.satuan.data');
});

Route::prefix('master')->controller(RoleController::class)->middleware('auth')->group(function () {
    Route::resource('role', RoleController::class);
});

Route::prefix('master')->controller(SupplierController::class)->middleware('auth')->group(function () {
    Route::get('supplier', 'index')->name('master.supplier');
    Route::get('supplier-list', 'datatable')->name('master.supplier.datatable');
    Route::post('supplier-add', 'store')->name('master.supplier.add');
    Route::post('supplier-edit', 'update')->name('master.supplier.edit');
    Route::post('supplier-delete', 'destroy')->name('master.supplier.delete');
});

Route::prefix('master')->controller(SalesController::class)->middleware('auth')->group(function () {
    Route::get('sales', 'index')->name('master.sales');
    Route::get('sales-list', 'datatable')->name('master.sales.list');
    Route::post('sales-add', 'store')->name('master.sales.add');
    Route::post('sales-edit', 'update')->name('master.sales.edit');
    Route::post('sales-delete', 'destroy')->name('master.sales.delete');
});


Route::prefix('laporan')->controller(LaporanController::class)->middleware('auth')->group(function () {
    Route::get('hutang-vendor', 'dataLHV')->name('laporan.LHV');
    Route::get('dash-monitor', 'dashMonitor')->name('laporan.dash.monitor');
    Route::get('lap-fee-sales', 'dataFLS')->name('laporan.FLS');
    Route::get('lap-sales', 'dataLS')->name('laporan.LS');
    Route::get('lap-omzet-cust', 'dataLOC')->name('laporan.LOC');
    Route::get('piutang-customer', 'dataLPC')->name('laporan.LPC');
    Route::get('lap-penjualan-harian', 'LapPenjualan')->name('laporan.LaporanPenjualanHarian');
    Route::get('data-lap-piutang', 'dataLapPiutang')->name('laporan.DataPiutang');
    Route::get('data-lap-penjualan-harian', 'dataPenjualanHarian')->name('laporan.DataPenjualanHarian');
    Route::get('data-total-lap-piutang', 'dataLapPiutangTotal')->name('laporan.TotalDataPiutang');
    Route::get('lap-piutang', 'LapPiutang')->name('laporan.Piutang');
    Route::get('rekap-pembayaran-harian', 'monitoring_invoice')->name('monitor.Invoice');
    Route::post('monitor-invoice-simpan', 'monitorSave')->name('monitorInv.store');
    Route::get('data-monitor-invoice', 'jqgrid1')->name('biaya.monitoring.data');
    Route::get('data-list-inv', 'listInv')->name('list.inv.data');
    Route::delete('biaya-inv/{id}', 'destroy')->name('biaya-inv.destroy');
});

// Route::prefix('jurnal')->controller(CoaController::class)->middleware('auth')->group(function () {
//     Route::get('coa', 'index')->name('jurnal.coa');
//     Route::post('coa', 'statusCoa')->name('jurnal.coa');
// });

// Route::prefix('jurnal')->controller(TemplateJurnalController::class)->middleware('auth')->group(function () {
//     Route::get('template-jurnal', 'index')->name('jurnal.template-jurnal');
//     Route::get('template-jurnal-create', 'create')->name('jurnal.template-jurnal.create');
// });

Route::get('/invoice', function () {
    $surat_jalan = SuratJalan::all();
    return view('keuangan.invoice_pdf', compact('surat_jalan'));
});

Route::get('/invoice_pdf/{id}', [KeuanganController::class, 'generatePDF'])->name('invoice.print');
Route::get('/sp_pdf/{id}', [KeuanganController::class, 'generatePDF'])->name('sp.print');


require __DIR__ . '/auth.php';
