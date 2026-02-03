<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceAbResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'id_ab'     => $this->id_ab ?? '-',

            // ===== ORDER / TARIF / ALAT =====
            'nama_alat' => $this->orders?->tarif?->alatBerat?->nama_alat ?? '-',
            'tarif'     => $this->orders?->tarif?->tarif ?? '-',
            'tarif_id'  => $this->orders?->tarif_id ?? '-',

            // ===== INVOICE =====
            'customers_id' => $this->penerima ?? '-',
            'tanggal_dari' => $this->tanggal_order ?? '-',
            'kode_invoice' => $this->kode_invoice ?? '-',
            'no'           => $this->no ?? '-',
            'total'        => $this->total ?? '-',
            'tgl_invoice'  => $this->tgl_invoice ?? '-',
            'sampai'       => $this->sampai ?? '-',

            // ===== CUSTOMER =====
            'nama_customers'           => $this->customerAb?->nama ?? '-',
            'nama_customers_npwp'      => $this->customerAb?->nama_npwp ?? '-',
            'alamat_customers'         => $this->customerAb?->alamat ?? '-',
            'alamat_customers_npwp'    => $this->customerAb?->alamat_npwp ?? '-',
            'customers_npwp'           => $this->customerAb?->npwp ?? '-',
            'customers_kota'           => $this->customerAb?->kota ?? '-',
            'customers_nik'            => $this->customerAb?->nik ?? '-',
            'customers_no_telp'        => $this->customerAb?->no_telp ?? '-',
        ];
    }
}
