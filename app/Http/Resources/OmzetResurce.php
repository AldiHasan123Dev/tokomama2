<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OmzetResurce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $tgl_pembayaran_pembelian = '-';
        $nomor_vocher_pembelian = '-';

        $tgl_pembayaran_penjualan = '-';
        $nomor_vocher_penjualan = '-';

        // Menggunakan get() untuk mengambil banyak hasil
        $checkInvx = $this->transaksi->jurnals()
            ->where('invoice_external', '!=', '0')
            ->whereIn('coa_id', [1, 5])
            ->where('kredit', '>', 0)
            ->get();

        $checkInv = $this->transaksi->jurnals()
            ->where('invoice', '!=', '0')
            ->whereIn('coa_id', [1, 5])
            ->where('debit', '>', 0)
            ->get();

        // Mengambil data dari hasil get() pertama
        if ($checkInvx->isNotEmpty()) {
            $tgl_pembayaran_pembelian = $checkInvx->first()->tgl ?? '-';
            $nomor_vocher_pembelian = $checkInvx->first()->nomor ?? '-';
        }

        if ($checkInv->isNotEmpty()) {
            $tgl_pembayaran_penjualan = $checkInv->first()->tgl ?? '-';
            $nomor_vocher_penjualan = $checkInv->first()->nomor ?? '-';
        }
        $ppnStatus = $this->transaksi->barang->status_ppn; 
        $ppnValue = $this->transaksi->barang->value_ppn;
        $ppnRate = 0.11; 

        // Inisialisasi default
        $subtotal = $this->subtotal; // Tetap menggunakan subtotal yang ada
        $ppnAmount = 0; // Default PPN amount adalah 0
        $total = $subtotal; // Default total adalah subtotal
        $ppnDisplay = '0%'; // Default PPN display
        $HJB = round($this->transaksi->harga_beli * $this->transaksi->jumlah_beli);
        $HJJ = round($this->transaksi->harga_jual * $this->transaksi->jumlah_beli);
        $marginExc = round(($this->transaksi->harga_jual * $this->transaksi->jumlah_beli) - ($this->transaksi->harga_beli * $this->transaksi->jumlah_beli));
        $ppnBeli = $HJB ;
        $ppnJual = $HJJ;
        $ppnMargin = $HJJ - $HJB;
        $ppnCekMargin = 0;
        // Logika perhitungan PPN
        if ($ppnValue == 11) {
            // Hanya pertimbangkan nilai PPN jika value PPN adalah 11
            if ($ppnStatus === 'ya') {
                $ppnJual = round(($this->transaksi->harga_jual + ($this->transaksi->harga_jual * 1.11)) *  $this->transaksi->jumlah_beli);
                $ppnBeli = round(($this->transaksi->harga_beli + ($this->transaksi->harga_beli * 1.11)) *  $this->transaksi->jumlah_beli);
                $ppnMargin = round($ppnJual - $ppnBeli);
                $ppnCekMargin = round($marginExc  * 1.11);
            }
        }



        return [
            //table invoice
            'id_invoice' => $this->id ?? '-',
            'id_nsfp' => $this->id_nsfp ?? '-',
            'id_transaksi' => $this->id_transaksi ?? '-',
            'invoice' => $this->invoice ?? '-',
            'harga' => $this->harga ?? '-',
            'jumlah' => $this->jumlah,
            'sub_total' => $this->sub_total ?? '-',
            'no' => $this->no ?? '-',
            'tgl_invoice' => $this->tgl_invoice ?? '-',
            'tgl_stuffing' => $this->transaksi->SuratJalan->tgl_sj ?? '-',
            'nomor_sj' => $this->transaksi->SuratJalan->nomor_surat ?? '-',
            'nomor_nsfp' => $this->NSFP->nomor ?? '-',
            'po_customer' => $this->transaksi->SuratJalan->no_po,
            'customer' => $this->transaksi->SuratJalan->Customer->nama ?? '-',
            'kota_cust' => $this->transaksi->SuratJalan->Customer->kota,
            'nama_kapal' => $this->transaksi->SuratJalan->nama_kapal ?? '-',
            'cont' => $this->transaksi->SuratJalan->no_cont ?? '-',
            'seal' => $this->transaksi->SuratJalan->no_seal ?? '-',
            'job' => $this->transaksi->SuratJalan->no_job ?? '-',
            'nopol' => $this->transaksi->SuratJalan->no_pol ?? '-',
            'nama_barang' => $this->transaksi->Barang->nama ?? '-',
            'qty' => $this->transaksi->jumlah_beli ?? '-',
            'satuan' => $this->transaksi->satuan_beli ?? '-',
            'harga_jual' => $this->transaksi->harga_jual ?? '-',
            'total_tagihan' => $this->transaksi->jumlah_beli * $this->transaksi->harga_jual ?? '-',
            'supplier' => $this->transaksi->Suppliers->nama ?? '-',
            'harga_beli' => $this->transaksi->harga_beli ?? '-',
            'total' => $this->transaksi->harga_beli * $this->transaksi->jumlah_beli ?? '-',
            'tgl_pembayaranpbl' => $tgl_pembayaran_pembelian,
            'no_vocherpbl' =>  $nomor_vocher_pembelian, 
            'tgl_penjualan' => $tgl_pembayaran_penjualan,
            'no_vocherpenj' => $nomor_vocher_penjualan,
            'harga_jual_ppn' => $ppnJual,
            'harga_beli_ppn' => $ppnBeli != 0 ? $ppnBeli : '-',
            'margin_ppn' => $ppnMargin,
            'margin' => ($this->transaksi->harga_jual * $this->transaksi->jumlah_beli) - ($this->transaksi->harga_beli * $this->transaksi->jumlah_beli) ?? '-',
            'margin_cek' => $ppnCekMargin,
            'satuan_standar' => $this->transaksi->Barang->Satuan->nama_satuan ?? '-',
            'beli' => ($this->transaksi->satuan_beli == $this->transaksi->Barang->Satuan->nama_satuan) ? $this->transaksi->harga_beli : $this->transaksi->harga_beli / $this->transaksi->Barang->value ?? '-',
            'jual' => ($this->transaksi->satuan_beli == $this->transaksi->Barang->Satuan->nama_satuan) ? $this->transaksi->harga_jual : $this->transaksi->harga_jual / $this->transaksi->Barang->value ?? '-'
        ];
    }
}
