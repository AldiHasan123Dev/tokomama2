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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_surat_jalan')->nullable()->constrained('surat_jalan')->onDelete('cascade');
            $table->foreignId('id_barang')->nullable()->constrained('barang')->onDelete('cascade');
            $table->double('harga_jual')->default(0);
            $table->double('jumlah_jual')->default(0);
            $table->string('satuan_jual')->nullable();
            $table->double('harga_beli')->default(0);
            $table->double('jumlah_beli')->default(0);
            $table->string('satuan_beli')->nullable();
            $table->double('margin')->default(0);
            $table->integer('sisa')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
