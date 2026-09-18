<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DraftInvoiceResource extends JsonResource
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
            'id_surat_jalan' => $this->id_sj ?? '-',
            'id_transaksi' => $this->id_transaksi ?? '-',
            'id_barang' => $this->transaksi->id_barang ?? '-',
            'harga_jual' => $this->transaksi->harga_jual ?? '-',
            'jumlah_jual' => $this->transaksi->jumlah_jual ?? '-',
            'sisa' => $this->transaksi->sisa ?? '-',
            'satuan_jual' => $this->transaksi->satuan_jual ?? '-',
            'harga_beli' => $this->transaksi->harga_beli ?? '-',
            'jumlah_beli' => $this->transaksi->jumlah_beli ?? '-',
            'satuan_beli' => $this->transaksi->satuan_beli ?? '-',
            'margin' => $this->transaksi->margin ?? '-',
            'subtotal' => $this->subtotal ?? '-',
            'tgl_invoice' => $this->invoice->tgl_invoice ?? '-',
            'invoice' => $this->invoice->invoice ?? '-',
            'nomor_surat' => $this->suratJalan->nomor_surat ?? '-',
            'kepada' => $this->suratJalan->kepada ?? '-',
            'jumlah' => $this->suratJalan->jumlah ?? '-',
            'satuan' => $this->suratJalan->satuan ?? '-',
            'nama_kapal' => $this->suratJalan->nama_kapal ?? '-',
            'no_cont' => $this->suratJalan->no_cont ?? '-',
            'no_seal' => $this->suratJalan->no_seal ?? '-',
            'no_pol' => $this->suratJalan->no_pol ?? '-',
            'no_job' => $this->suratJalan->no_job ?? '-',
            'no_po' => $this->suratJalan->no_po ?? '-',
            'kota_pengirim' => $this->suratJalan->kota_pengirim ?? '-',
            'nama_pengirim' => $this->suratJalan->nama_pengirim ?? '-',
            'nama_penerima' => $this->suratJalan->nama_penerima ?? '-',
            'no' => $this->suratJalan->no ?? '-',
            'ppn' => $this->suratJalan->ppn ?? '-',
            'sub_total' => $this->suratJalan->sub_total ?? '-',
            'total' => $this->suratJalan->total ?? '-',
            'tgl_sj' => $this->suratJalan->tgl_sj ?? '-',
            'kode_objek' => $this->transaksi->Barang->kode_objek ?? '-',
            'nama_barang' => $this->transaksi->Barang->nama ?? '-',
            'value' => $this->transaksi->Barang->value ?? '-',
            'customer' => $this->suratJalan->customer->nama ?? '-',
            'harga' => $this->harga ?? '-',
            'jumlah' => $this->jumlah ?? '-',
            'draft_no' => $this->draft_no ?? '-',   
        ];
    }
}
