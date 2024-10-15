<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
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
            'id_nsfp' => $this->id_nsfp ?? '-',
            'id_transaksi' => $this->id_transaksi ?? '-',
            'invoice' => $this->invoice ?? '-',
            'harga' => $this->harga ?? '-',
            'jumlah' => $this->jumlah ?? '-',
            'sub_total' => $this->sub_total ?? '-',
            'no' => $this->no ?? '-',
            'tgl_invoice' => $this->tgl_invoice ?? '-',
            'id_surat_jalan' => $this->Transaction->id_surat_jalan ?? '-',
            'id_barang' => $this->Transaction->id_barang ?? '-',
            'harga_jual' => $this->Transaction->harga_jual ?? '-',
            'jumlah_jual' => $this->Transaction->jumlah_jual ?? '-',
            'satuan_jual' => $this->Transaction->satuan_jual ?? '-',
            'harga_beli' => $this->Transaction->harga_beli ?? '-',
            'jumlah_beli' => $this->Transaction->jumlah_beli ?? '-',
            'satuan_beli' => $this->Transaction->satuan_beli ?? '-',
            'margin' => $this->Transaction->margin ?? '-',
            'sisa' => $this->Transaction->sisa ?? '-',
            'keterangan' => $this->Transaction->keterangan ?? '-',
        ];
    }
}
