<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SuratJalanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $ppnStatus = $this->transaksi->barang->status_ppn; 
        $ppnValue = $this->transaksi->barang->value_ppn;
        $ppnRate = 0.11; 

        // Inisialisasi default
        $subtotal = $this->subtotal; // Tetap menggunakan subtotal yang ada
        $ppnAmount = 0; // Default PPN amount adalah 0
        $total = $subtotal; // Default total adalah subtotal
        $ppnDisplay = '0%'; // Default PPN display

        // Logika perhitungan PPN
        if ($ppnValue == 11) {
            // Hanya pertimbangkan nilai PPN jika value PPN adalah 11
            if ($ppnStatus === 'ya') {
                // Jika status PPN 'ya' atau 'tidak', PPN dihitung jika value PPN adalah 11
                $ppnAmount = $subtotal * $ppnRate;
                $total = $subtotal + $ppnAmount;
                $ppnDisplay = '11%';
            }
        }

        return [
            'id' => $this->id,
            'invoice' => $this->invoice ?? '-',
            'tgl_invoice' => $this->tgl_invoice ?? '-',
            'npwp' => $this->transaksi->suratJalan->customer->npwp ?? '-',
            'nik' => $this->transaksi->suratJalan->customer->nik ?? '-',
            'nama' => $this->transaksi->suratJalan->customer->nama ?? '-',
            'nama_npwp' => $this->transaksi->suratJalan->customer->nama_npwp ?? '-',
            'alamat_npwp' => $this->transaksi->suratJalan->customer->alamat_npwp ?? '-',
            'tujuan' => $this->transaksi->suratJalan->customer->nama ?? '-',
            'uraian' => $this->transaksi->barang->nama ?? '-',
            'faktur' => $this->nsfp->nomor ?? '-',
            'subtotal' => $subtotal ?? '-',
            'ppn' => $ppnDisplay ?? '-',
            'nominal_ppn' => $ppnAmount ?? '-',
            'total' => $total ?? '-',
            'none' => '-',
        ];
    }

}
