<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('jurnal_invoice')->nullable();
            $table->date('tgl_invoice')->nullable();
            $table->string('invoice')->nullable();
            $table->string('nomor_surat')->nullable();
            $table->string('kepada')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan')->nullable();
            // $table->integer('total')->nullable();
            $table->string('nama_kapal')->nullable();
            $table->string('no_cont')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('no_pol')->nullable();
            $table->string('no_job')->nullable();
            $table->string('no_po')->nullable();
            $table->string('kota_pengirim')->default('surabaya')->nullable();
            $table->string('nama_pengirim')->default('FIRDA')->nullable();
            $table->string('nama_penerima')->default('IFAN')->nullable();
            $table->integer('no')->default(0);
            $table->double('ppn')->default(0);
            $table->double('subtotal')->default(0);
            $table->double('total')->default(0)->nullable();
            $table->foreignId('id_ekspedisi')->nullable()->constrained('ekspedisi');
            $table->foreignId('id_customer')->nullable()->constrained('customer');
            $table->foreignId('id_nsfp')->nullable()->constrained('nsfp');
            $table->date('tgl_sj')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', precision: 0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan');
    }
};
