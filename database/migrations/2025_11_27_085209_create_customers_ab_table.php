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
        Schema::create('customers_ab', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('nama_npwp')->nullable();
            $table->string('alamat')->nullable();
            $table->string('alamat_npwp')->nullable();
            $table->string('npwp')->nullable();
            $table->string('nik')->nullable();
            $table->string('no_telp')->nullable();
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
        Schema::dropIfExists('customers_ab');
    }
};
