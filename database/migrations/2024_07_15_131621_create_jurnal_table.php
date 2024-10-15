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
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coa_id')->constrained('coa');
            $table->string('nomor');
            $table->date('tgl');
            $table->string('keterangan');
            $table->string('keterangan_buku_besar_pembantu')->nullable();
            $table->double('debit')->default(0);
            $table->double('kredit')->default(0);
            $table->string('invoice')->nullable();
            $table->string('nopol')->nullable();
            $table->string('container')->nullable();
            $table->enum('tipe',['BBK','BBM','BKK','BKM','JNL']);
            $table->string('no')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal');
    }
};
