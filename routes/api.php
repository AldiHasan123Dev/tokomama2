<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\NSFPController;
use App\Http\Controllers\Api\SuratJalanController;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\JurnalController;
use App\Http\Controllers\JurnalManualController;
use App\Http\Controllers\KeuanganController;
use App\Http\Resources\DatatableCollection;
use App\Http\Resources\DatatableResource;
use App\Http\Resources\PajakResource;
use App\Models\NSFP;
use Illuminate\Http\Request;
use App\Http\Resources\SuratJalanCollection;
use App\Models\Role;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('generate-nsfp', [NSFPController::class, 'generate'])->name('api.nsfp.generate');
Route::get('/nsfpcolection', [NSFPController::class, 'data'])->name('nsfp.data');
Route::get('/nsfp_with_invoice', [NSFPController::class, 'dataNSFPDone'])->name('nsfp.done');
Route::post('/nsfp_delete_all', [NSFPController::class, 'deleteAllNSFP']) ->name('nsfp.delete-all');
Route::post('/nsfp_delete', [NSFPController::class, 'deleteNSFP']) ->name('nsfp.delete');
Route::post('/nsfp_edit', [NSFPController::class, 'update'])->name('nsfp.edit');


Route::post('/invoice', [KeuanganController::class, 'dataTable'])->name('invoice.data');

Route::post('/coa', [CoaController::class, 'dataTable'])->name('coa.data');
Route::post('/jurnal-data', [JurnalController::class, 'dataTable'])->name('jurnal.data');

Route::get('/pre-invoice', [InvoiceController::class, 'dataTable'])->name('invoice.pre-invoice');
Route::post('/pre-invoice', [InvoiceController::class, 'ambil'])->name('invoice.pre-invoice.ambil');

