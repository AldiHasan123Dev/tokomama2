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
        Schema::table('template_jurnal_item', function (Blueprint $table) {
            $table->dropForeign(['template_jurnal_id']);
            $table->foreign('template_jurnal_id')
                ->references('id')
                ->on('template_jurnal')
                ->onDelete('cascade');

            $table->dropForeign(['coa_debit_id']);
            $table->foreign('coa_debit_id')
                ->references('id')
                ->on('coa')
                ->onDelete('cascade')->null();

            $table->dropForeign(['coa_kredit_id']);
            $table->foreign('coa_kredit_id')
                ->references('id')
                ->on('coa')
                ->onDelete('cascade')->null();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_jurnal_item', function (Blueprint $table) {
            // You can reverse the changes here if needed
        });
    }
};
