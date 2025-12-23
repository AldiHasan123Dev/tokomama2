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
        Schema::create('invoice_ab', function (Blueprint $table) {
            $table->id();
            // Relasi ke tarif alat berat
            $table->foreignId('id_order')
                  ->constrained('order')
                  ->cascadeOnDelete();
            // Data invoice
            $table->integer('no')->nullable(); // nomor invoice otomatis
            $table->string('kode_invoice')->nullable();
            $table->string('penerima')->nullable();
            $table->date('tanggal_invoice')->nullable();
            $table->date('sampai')->nullable();
            $table->string('barang')->nullable();
            $table->integer('total_jam')->nullable();
            // Jumlah total
            $table->double('total')->default(0);
            // User trace
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_ab');
    }
};
