<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id' => $this->id,
            'id_ab' => $this->tarif->id_ab ?? '-',
            'keterangan' => $this->keterangan ?? '-',
            'barang' => $this->barang ?? '-',
            'tanggal_order' => $this->tanggal_order ?? '-',
            'nama_alat' => $this->tarif->alatBerat->nama_alat ?? '-',
            'tarif' => $this->tarif->tarif,
            'tarif_id' => $this->tarif_id ?? '-',
            'customers_id' => $this->customers_id ?? '-',
            'tanggal_dari' => $this->tanggal_order ?? '-',
            'id_customers' => $this->customersAb->id ?? '-',
            'nama_customers' => $this->customersAb->nama ?? '-',
            'nama_customers_npwp' => $this->customersAb->nama_npwp ?? '-',
            'alamat_customers' => $this->customersAb->alamat ?? '-',
            'alamat_customers_npwp' => $this->customersAb->alamat_npwp ?? '-',
            'customers_npwp' => $this->customersAb->npwp ?? '-',
            'customers_kota' => $this->customersAb->kota ?? '-',
            'customers_nik' => $this->customersAb->nik ?? '-',
            'customers_no_telp' => $this->customersAb->no_telp ?? '-',
        ];
    }
}
