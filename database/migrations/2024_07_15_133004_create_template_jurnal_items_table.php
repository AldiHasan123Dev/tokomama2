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
        Schema::create('template_jurnal_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_jurnal_id')->constrained('template_jurnal');
            $table->foreignId('coa_debit_id')->nullable()->constrained('coa');
            $table->foreignId('coa_kredit_id')->nullable()->constrained('coa');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_jurnal_item');
    }
};
