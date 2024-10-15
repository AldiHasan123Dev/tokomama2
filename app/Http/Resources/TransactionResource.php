<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'id_surat_jalan' => $this->id_surat_jalan ?? '-',
            'id_barang' => $this->id_barang ?? '-',
            'harga_jual' => $this->harga_jual ?? '-',
            'jumlah_jual' => $this->jumlah_jual ?? '-',
            'sisa' => $this->sisa ?? '-',
            'satuan_jual' => $this->satuan_jual ?? '-',
            'harga_beli' => $this->harga_beli ?? '-',
            'jumlah_beli' => $this->jumlah_beli ?? '-',
            'satuan_beli' => $this->satuan_beli ?? '-',
            'margin' => $this->margin ?? '-',
            'subtotal' => $this->jumlah_jual * $this->harga_jual ?? '-',
            'tgl_invoice' => $this->SuratJalan->tgl_invoice ?? '-',
            'invoice' => $this->Invoice->invoice ?? '-',
            'nomor_surat' => $this->SuratJalan->nomor_surat ?? '-',
            'kepada' => $this->SuratJalan->kepada ?? '-',
            'jumlah' => $this->SuratJalan->jumlah ?? '-',
            'satuan' => $this->SuratJalan->satuan ?? '-',
            'nama_kapal' => $this->SuratJalan->nama_kapal ?? '-',
            'no_cont' => $this->SuratJalan->no_cont ?? '-',
            'no_seal' => $this->SuratJalan->no_seal ?? '-',
            'no_pol' => $this->SuratJalan->no_pol ?? '-',
            'no_job' => $this->SuratJalan->no_job ?? '-',
            'no_po' => $this->SuratJalan->no_po ?? '-',
            'kota_pengirim' => $this->SuratJalan->kota_pengirim ?? '-',
            'nama_pengirim' => $this->SuratJalan->nama_pengirim ?? '-',
            'nama_penerima' => $this->SuratJalan->nama_penerima ?? '-',
            'no' => $this->SuratJalan->no ?? '-',
            'ppn' => $this->SuratJalan->ppn ?? '-',
            'sub_total' => $this->SuratJalan->sub_total ?? '-',
            'total' => $this->SuratJalan->total ?? '-',
            'tgl_sj' => $this->SuratJalan->tgl_sj ?? '-',
            'kode_objek' => $this->Barang->kode_objek ?? '-',
            'nama_barang' => $this->Barang->nama ?? '-',
            'value' => $this->Barang->value ?? '-',
            'customer' => $this->SuratJalan->customer->nama ?? '-',
        ];
    }
}
