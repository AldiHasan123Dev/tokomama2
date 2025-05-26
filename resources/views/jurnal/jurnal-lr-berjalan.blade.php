<x-Layout.layout>
    <x-keuangan.card-keuangan>
                <div class="overflow-x-auto">
          
          
            <div>
            <div>
                {{-- <a href="#" target="_blank"
                    class="btn bg-green-400 text-white my-5 py-4 font-bold" id="print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Laporan</button>
                </a> --}}
                <div class="flex justify-between">
                <div>
                    <label for="bulan" class="mr-2 mt-10">Bulan:</label>
                    @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Des'] as $index => $bulanName)
                        <button id="bulan-{{ $index + 1 }}" 
                            class="btn my-5 py-4 font-bold border-black 
                                {{ $index + 1 == $bulan ? 'bg-green-600 text-white' : 'bg-white text-black hover:bg-green-600 hover:text-white' }}" 
                            data-bulan="{{ $index + 1 }}" 
                            onclick="filterBulan({{ $index + 1 }})">
                            {{ $bulanName }}
                        </button>
                    @endforeach
                </div>
                    <div class="flex items-center">
                        <b class="mr-2 margin-top:40px">Tahun:</b>
                        <select class="form-control-bordered rounded-lg dark:text-black my-7 py-3 w-24 pl-2" id="tahun" onchange="filterBulanAndYear()">
                            @for($year = 2024; $year <= 2030; $year++)
                                <option value="{{ $year }}" {{ $year == $tahun ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

             @php
                // Ambil data total untuk bulan yang dipilih, default 0 jika tidak ada
                $dataTotal = $totalsPerBulanFinal[$bulan] ?? [
                    'totalA' => 0,
                    'totalB' => 0,
                    'totalC' => 0,
                    'totalD' => 0,
                    'totalE' => 0,
                    'totalF' => 0,
                    'totalG' => 0,
                ];

                $totalA = $dataTotal['totalA'];
                $totalB = $dataTotal['totalB'];
                $totalC = $dataTotal['totalC'];
                $totalD = $dataTotal['totalD'];
                $totalE = $dataTotal['totalE'];
                $totalF = $dataTotal['totalF'];
                $totalG = $dataTotal['totalG'];

                $kotor = $totalA - $totalB;
                $usaha = $kotor - $totalC - $totalD;
                $bersih = $usaha - $totalE - $totalF;
                $pajak = $bersih - $totalG;
            @endphp


            <div class="flex justify-between">
                <div class="w-7/12">
                            <table class="table-auto w-full text-xs">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="px-4 py-2 text-left">A. PENJUALAN USAHA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- masukkan logic -->
                                    @foreach($coa1 as $item)
                                        @php
                                        
                                        $total = $totals[$item->id] ?? ['pendapatan' => 0];
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                        <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                        
                                        <td class="border px-4 py-2 text-right">{{ number_format($total['pendapatan'] , 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <!-- masukkan logic -->
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL</td>
                                            
                                            <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalA , 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </tbody>
                            </table>
                            <table class="table-auto w-full text-xs">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="px-4 py-2 text-left">B. HARGA POKOK PENJUALAN</th>
                                    </tr>
                                    </thead>
                                <tbody>
                                    <!-- masukkan logic -->
                                    @foreach($coa2 as $item)
                                        @php                                        
                                        $total = $totals[$item->id] ?? ['selisih' => 0];
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                        <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                        <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'] , 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <!-- masukkan logic -->
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL</td>
                                            <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalB , 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </tbody>
                            </table>
                            <table class="table-auto w-full text-xs">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="px-4 py-2 text-left">C. BIAYA USAHA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- masukkan logic -->
                                    @foreach($coa3 as $item)
                                        @php                                        
                                        $total = $totals[$item->id] ?? ['selisih' => 0];
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                        <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                        
                                        <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'] , 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <!-- masukkan logic -->
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL</td>
                                            <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalC , 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </tbody>
                            </table>
                            <table class="table-auto w-full text-xs">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="px-4 py-2 text-left">D. BIAYA DEPRESIASI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- masukkan logic -->
                                    @foreach($coa4 as $item)
                                        @php                                        
                                        $total = $totals[$item->id] ?? ['selisih' => 0];
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                        <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                        <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'] , 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <!-- masukkan logic -->
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL</td>
                                            <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalD , 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </tbody>
                            </table>
                            <table class="table-auto w-full text-xs">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="px-4 py-2 text-left">E. PENDAPATAN DAN BIAYA LAIN-LAIN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- masukkan logic -->
                                    @foreach($coa5 as $item)
                                        @php                                        
                                        $total = $totals[$item->id] ?? ['selisih' => 0];
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                        <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                        <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'] , 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <!-- masukkan logic -->
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL</td>
                                            <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalE , 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </tbody>
                            </table>
                            <table class="table-auto w-full text-xs">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="px-4 py-2 text-left">F. BIAYA KEUANGAN I</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- masukkan logic -->
                                    @foreach($coa6 as $item)
                                        @php                                        
                                        $total = $totals[$item->id] ?? ['selisih' => 0];
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                        <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                        <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'] , 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <!-- masukkan logic -->
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL</td>
                                            <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalF , 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </tbody>
                            </table>
                            <table class="table-auto w-full mt-3 text-xs">
                                <thead>
                                    <tr>
                                        <th colspan="3" class="px-4 py-2 text-left">G. BIAYA KEUANGAN II</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- masukkan logic -->
                                    @foreach($coa7 as $item)
                                        @php                                        
                                        $total = $totals[$item->id] ?? ['selisih' => 0];
                                    @endphp
                                    <tr>
                                        <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                        <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                        <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'] , 2, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <!-- masukkan logic -->
                                    <tfoot>
                                        <tr>
                                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL</td>
                                            <td class="border px-4 py-2 text-right font-bold">{{ number_format($totalG , 2, ',', '.') }}</td>
                                        </tr>
                                    </tfoot >
                                </tbody>
                            </table>
                        </div>

            <x-slot:tittle>Laporan Laba/Rugi Berjalan</x-slot:tittle>
            <table class="w-auto h-3/4 text-xs border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="text-black font-bold px-2 py-1 text-left">Summary</th>
                        <th class="text-black font-bold px-2 py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t">
                        <td class="px-2 py-1">TOTAL PENJUALAN USAHA</td>
                        <td class="px-2 py-1 text-right">{{ number_format($totalA, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-2 py-1">TOTAL HARGA POKOK PENJUALAN</td>
                        <td class="px-2 py-1 text-right">{{ number_format($totalB, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t bg-gray-50">
                        <td class="px-2 py-1">LABA/RUGI KOTOR</td>
                        <td class="px-2 py-1 text-right">{{ number_format($kotor, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-2 py-1">TOTAL BIAYA USAHA</td>
                        <td class="px-2 py-1 text-right">{{ number_format($totalC, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-2 py-1">TOTAL BIAYA PENYUSUTAN</td>
                        <td class="px-2 py-1 text-right">{{ number_format($totalD, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t bg-gray-50">
                        <td class="px-2 py-1">LABA/RUGI USAHA</td>
                        <td class="px-2 py-1 text-right">{{ number_format($usaha, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-2 py-1">TOTAL PENDAPATAN DAN BIAYA LAIN-LAIN</td>
                        <td class="px-2 py-1 text-right">{{ number_format($totalE, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-2 py-1">TOTAL BIAYA KEUANGAN I</td>
                        <td class="px-2 py-1 text-right">{{ number_format($totalF, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t bg-gray-50">
                        <td class="px-2 py-1">LABA/RUGI BERSIH SEBELUM PAJAK</td>
                        <td class="px-2 py-1 text-right">{{ number_format($bersih, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t">
                        <td class="px-2 py-1">TOTAL BIAYA KEUANGAN II</td>
                        <td class="px-2 py-1 text-right">{{ number_format($totalG, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t bg-gray-50">
                        <td class="px-2 py-1">LABA/RUGI BERSIH SESUDAH PAJAK</td>
                        <td class="px-2 py-1 text-right">{{ number_format($pajak, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    <script>
        function filterBulan(bulan) {
            const tahun = document.getElementById('tahun').value;
            window.location.href = `?bulan=${bulan}&tahun=${tahun}`;
        }

        function filterBulanAndYear() {
            const bulanSelected = [...document.querySelectorAll('button[id^="bulan-"]')].find(btn => btn.classList.contains('bg-green-600')).dataset.bulan;
            const tahun = document.getElementById('tahun').value;
            window.location.href = `?bulan=${bulanSelected}&tahun=${tahun}`;
        }
    </script>
</x-Layout.layout>
