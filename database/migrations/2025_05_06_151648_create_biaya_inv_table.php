<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiayaInvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biaya_inv', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('id_trans')->constrained('transaksi')->onDelete('cascade'); // Foreign key to transaksi
            $table->foreignId('id_inv')->constrained('invoice')->onDelete('cascade'); // Foreign key to invoice
            $table->double('nominal')->default(0); // Nominal with 2 decimal points
            $table->date('tgl_pembayar')->nullable(); // Date of payment
            $table->softDeletes(); // Soft delete column
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biaya_inv');
    }
}
