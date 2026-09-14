<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draft_invoices', function (Blueprint $table) {
            // Menambahkan kolom invoice_id dan relasi ke tabel invoices
            $table->unsignedBigInteger('invoice_id')->nullable()->after('id');

            // Membuat foreign key ke tabel invoices
            $table->foreign('invoice_id')
                  ->references('id')
                  ->on('invoice')
                  ->onDelete('cascade'); 
        });
    }

    public function down(): void
    {
        Schema::table('draft_invoices', function (Blueprint $table) {
            // Hapus foreign key dulu sebelum drop kolom
            $table->dropForeign(['invoice_id']);
            $table->dropColumn('invoice_id');
        });
    }
};
