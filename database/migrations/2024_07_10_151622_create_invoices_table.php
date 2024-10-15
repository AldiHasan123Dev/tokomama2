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
        Schema::create('invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_nsfp')->constrained('nsfp');
            $table->foreignId('id_transaksi')->constrained('transaksi');
            $table->string('invoice')->nullable();
            $table->double('harga')->default(0);
            $table->double('jumlah')->default(0);
            $table->double('subtotal')->default(0);
            $table->integer('no')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice');
    }
};
