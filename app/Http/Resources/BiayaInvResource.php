<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiayaInvResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'customer'     => $this->transaksi->suratJalan->customer->nama,
            'piutang'     => round($this->invoice->subtotal), // gunakan relasi jika sudah eager loaded
            'invoice'      => $this->invoice->invoice,
            'tgl_inv'      => $this->invoice->tgl_invoice,
            'bayar'      => (int) $this->nominal,
            'tgl_pembayar' => $this->tgl_pembayar,
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }
}
