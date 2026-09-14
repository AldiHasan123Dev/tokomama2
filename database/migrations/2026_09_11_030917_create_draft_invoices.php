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
        Schema::create('draft_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_sj')->constrained('surat_jalan');
            $table->foreignId('id_transaksi')->constrained('transaksi');
            $table->date('tanggal')->nullable();
            $table->double('harga')->default(0);
            $table->double('jumlah')->default(0);
            $table->double('subtotal')->default(0);
            $table->string('draft_no')->default(0);
            $table->integer('no')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_invoices');
    }
};
