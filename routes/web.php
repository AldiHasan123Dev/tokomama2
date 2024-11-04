<?php

use App\Http\Controllers\Api\NSFPController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\BukuBesarPembantuController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceExternalController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\JurnalManualController;
use App\Http\Controllers\NSFPController as nsfp;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LabaRugi;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Neraca;
use App\Http\Controllers\NopolController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\TemplateJurnalController;
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
// Route::get('test', function () {
//     $data1 = SuratJalan::get();
//     $data = SuratJalanResource::collection($data1);
//     $res = $data->toArray(request());
//     return response($data);
// });
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/surat-jalan-cetak/{surat_jalan}', [SuratJalanController::class, 'cetak'])->name('surat-jalan.cetak');
    Route::get('/surat-jalan-tarif-barang', [SuratJalanController::class, 'tarif'])->name('surat-jalan.barang');
    Route::get('/surat-jalan/editBarang', [SuratJalanController::class, 'editBarang'])->name('surat-jalan.editBarang');
    Route::post('/surat-jalan/editBarang', [SuratJalanController::class, 'editBarangPost'])->name('surat-jalan.editBarang');
    Route::delete('/surat-jalan/hapusBarang/', [SuratJalanController::class, 'hapusBarang'])->name('surat-jalan.hapusBarang');
    Route::post('/surat-jalan/tambahBarang', [SuratJalanController::class, 'tambahBarang'])->name('surat-jalan.tambahBarang');
    Route::post('/surat-jalan-data', [SuratJalanController::class, 'dataTable'])->name('surat-jalan.data');
    Route::post('/surat-jalan-supplier-data', [SuratJalanController::class, 'dataTableSupplier'])->name('surat-jalan-supplier.data');
    Route::post('/surat-jalan-edit', [SuratJalanController::class, 'update'])->name('surat-jalan.data.edit');
    Route::post('/surat-jalan-external-edit', [SuratJalanController::class, 'updateInvoiceExternal'])->name('surat-jalan-external.data.edit');
    Route::post('/surat-jalan-delete', [SuratJalanController::class, 'destroy'])->name('surat-jalan.data.delete');
    // Route::delete('/surat-jalan-delete', [SuratJalanController::class, 'destroy'])->name('surat-jalan.data.delete');

    Route::resource('surat-jalan', SuratJalanController::class);
    Route::resource('invoice-transaksi', InvoiceController::class);
    Route::post('/preview-invoice', [InvoiceController::class, 'preview'])->name('preview.invoice');
//    Route::resource('jurnal', JurnalController::class);
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/data-jurnal', [JurnalController::class, 'dataJurnal'])->name('jurnal.data');
    Route::get('/jurnal-edit', [JurnalController::class, 'edit'])->name('jurnal.edit');
    Route::get('/jurnal-merger', [JurnalController::class, 'merger'])->name('jurnal.jurnal-merger');
    Route::post('/jurnal-merger', [JurnalController::class, 'merger_store'])->name('jurnal.jurnal-merger');
    Route::post('/jurnal-update', [JurnalController::class, 'update'])->name('jurnal.edit.update');
    Route::post('/jurnal-delete', [JurnalController::class, 'destroy'])->name('jurnal.item.delete');
    Route::post('/jurnal-tgl-update', [JurnalController::class, 'tglUpdate'])->name('jurnal.edit.tglupdate');
    Route::get('/jurnal-edit-list', [JurnalController::class, 'datatableEdit'])->name('jurnal.edit.list');
    Route::get('/jurnal-transaksi', [JurnalManualController::class, 'transaksi'])->name('jurnal-manual-transaksi');
    Route::resource('jurnal-manual', JurnalManualController::class);
    Route::post('jurnal-hutang', [JurnalManualController::class, 'Jurnalhutang'])->name('jurnal.hutang');
    Route::post('/jurnal-manual-template', [JurnalManualController::class, 'terapanTemplateJurnal'])->name('jurnal.template.terapan');
    Route::post('jurnal-sj-wherejob', [JurnalManualController::class, 'getInvoiceWhereNoInv'])->name('jurnal.sj.whereInv');
    Route::post('jurnal-sj-whereinvext', [JurnalManualController::class, 'getInvoiceWhereNoInvExt'])->name('jurnal.sj.whereInvExt');
    Route::post('ekspedisi-data', [EkspedisiController::class, 'dataTable'])->name('ekspedisi.data');
    Route::post('transaction-data', [TransactionController::class, 'dataTable'])->name('transaksi.data');
    Route::put('transaction-update', [TransactionController::class, 'update'])->name('transaksi.update');
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
    Route::get('buku-besar-pembantu/{id}/detail', [BukuBesarPembantuController::class, 'showDetail'])->name('buku-besar-pembantu.showDetail');
    Route::get('/export-ncs', [BukuBesarPembantuController::class, 'exportNcs'])->name('export.ncs');
    Route::get('/export-customers', [BukuBesarPembantuController::class, 'exportCustomer'])->name('export.customers');
    Route::get('/export-supplier', [BukuBesarPembantuController::class, 'exportSupplier'])->name('export.supplier');

    Route::resource('invoice-external', InvoiceExternalController::class);
});

Route::prefix('keuangan')->controller(KeuanganController::class)->middleware('auth')->group(function () {
    Route::get('', 'index')->name('keuangan');
    Route::get('surat-jalan', 'suratJalan')->name('keuangan.surat-jalan');
    Route::post('surat-jalan', 'suratJalanStore')->name('keuangan.surat-jalan');
    Route::get('invoice', 'invoice')->name('keuangan.invoice');
    Route::get('pre-invoice', 'preInvoice')->name('keuangan.pre-invoice');
    Route::post('draf-invoice/{surat_jalan}', 'submitInvoice')->name('keuangan.invoice.submit');
    Route::get('draf-invoice/{surat_jalan}', 'invoiceDraf')->name('keuangan.invoice.draf');
    Route::get('cetak-invoice', 'cetakInvoice')->name('keuangan.invoice.cetak');
    Route::get('cetak-invoicesp', 'cetakInvoicesp')->name('keuangan.invoicesp.cetak');
    Route::get('omzet', 'omzet')->name('keuangan.omzet');
    Route::get('omzet-total', 'omzet_total')->name('keuangan.omzet-total');
    Route::get('data-omzet-total', 'dataOmzeTotal')->name('keuangan.data-omzet-total');
    Route::get('omzet-list', 'dataTableOmzet')->name('keuangan.omzet.datatable');
    Route::post('omzet-export', 'OmzetExportExcel')->name('keuangan.omzet.exportexcel');
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

Route::prefix('master')->controller(BarangController::class)->middleware('auth')->group(function () {
    Route::get('barang', 'index')->name('master.barang');
    Route::get('barang_list', 'datatable')->name('master.barang.list');
    Route::post('barang_add', 'store')->name('master.barang.add');
    Route::post('barang_edit', 'update')->name('master.barang.edit');
    Route::post('barang_delete', 'destroy')->name('master.barang.delete');
});

Route::prefix('master')->controller(NopolController::class)->middleware('auth')->group(function () {
    Route::get('nopol', 'index')->name('master.nopol');
    Route::get('nopol_list', 'datatable')->name('master.nopol.list');
    Route::post('nopol_add', 'store')->name('master.nopol.add');
    Route::post('nopol_edit', 'update')->name('master.nopol.edit');
    Route::post('nopol_delete', 'destroy')->name('master.nopol.delete');
    Route::post('set_status', 'setStatus')->name('master.nopol.editstatus');
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
