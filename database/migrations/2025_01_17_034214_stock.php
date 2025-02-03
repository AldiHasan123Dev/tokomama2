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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->nullable()->constrained('barang')->onDelete('cascade');
            $table->foreignId('id_supplier')->nullable()->constrained('suppliers')->onDelete('cascade');
            $table->date('tgl_beli')->nullable();
            $table->date('tgl_jual')->nullable();
            $table->integer('is_active')->default(1);
            $table->integer('vol_bm')->nullable();
            $table->integer('vol_bk')->nullable();
            $table->integer('sisa')->default(0);
            $table->timestamps();
            $table->softDeletes(); // Menambahkan kolom deleted_at untuk soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};