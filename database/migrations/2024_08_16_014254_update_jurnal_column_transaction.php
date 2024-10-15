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
        Schema::table('jurnal', function (Blueprint $table) {
            $table->unsignedBigInteger('id_transaksi')->nullable()->after('invoice_external');
            $table->foreign('id_transaksi')->references('id')->on('transaksi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jurnal', function (Blueprint $table) {
            $table->dropColumn('id_transaksi');
        });
    }
};
