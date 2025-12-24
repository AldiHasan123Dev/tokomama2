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
            'nama_alat' => $this->tarif->alatBerat->nama_alat ?? '-',
            'tarif' => $this->tarif->tarif,
            'tarif_id' => $this->tarif_id ?? '-',
            'customers_id' => $this->customers_id ?? '-',
            'tanggal_dari' => $this->tanggal_order ?? '-',
            'nama_customers' => $this->customers->nama ?? '-',
            'nama_customers_npwp' => $this->customers->nama_npwp ?? '-',
            'alamat_customers' => $this->customers->alamat ?? '-',
            'alamat_customers_npwp' => $this->customers->alamat_npwp ?? '-',
            'customers_npwp' => $this->customers->npwp ?? '-',
            'customers_kota' => $this->customers->kota ?? '-',
            'customers_nik' => $this->customers->nik ?? '-',
            'customers_no_telp' => $this->customers->no_telp ?? '-',
        ];
    }
}
