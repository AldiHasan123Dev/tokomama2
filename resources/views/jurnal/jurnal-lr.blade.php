<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Laporan Laba/Rugi</x-slot:tittle>
        <div class="overflow-x-auto">
          
          
            <div>
            <div>
                <a href="#" target="_blank"
                    class="btn bg-green-400 text-white my-5 py-4 font-bold" id="print" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Laporan</button>
                </a>
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
    
                    



                        <table class="w-auto h-3/4 text-xs border-collapse border border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="text-black font-bold px-2 py-1 text-left">Summarry</th>
                                    <th class="text-black font-bold px-2 py-1">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-t">
                                    <td class="px-2 py-1">TOTAL PENJUALAN USAHA</td>
                                    <td class="px-2 py-1">{{ number_format($totalA , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t ">
                                    <td class="px-2 py-1">TOTAL HARGA POKOK PENJUALAN</td>
                                    <td class="px-2 py-1">{{ number_format($totalB , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t bg-gray-50">
                                    <td class="px-2 py-1">LABA/RUGI KOTOR</td>
                                    
                                    <td class="px-2 py-1">{{ number_format($kotor = $totalA - $totalB , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-2 py-1">TOTAL BIAYA USAHA</td>
                                    <td class="px-2 py-1">{{ number_format($totalC , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-2 py-1">TOTAL BIAYA PENYUSUTAN</td>
                                    <td class="px-2 py-1">{{ number_format($totalD , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t bg-gray-50">
                                    <td class="px-2 py-1">LABA/RUGI USAHA</td>
                                    
                                    <td class="px-2 py-1">{{ number_format($usaha = $kotor - $totalC - $totalD , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-2 py-1">TOTAL PENDAPATAN DAN BIAYA LAIN-LAIN</td>
                                    <td class="px-2 py-1">{{ number_format($totalE , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-2 py-1">TOTAL BIAYA KEUANGAN I</td>
                                    <td class="px-2 py-1">{{ number_format($totalF , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t bg-gray-50">
                                    <td class="px-2 py-1">LABA/RUGI BERSIH SEBELUM PAJAK</td>
                                    <td class="px-2 py-1">{{ number_format($bersih = $usaha - $totalE - $totalF , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t">
                                    <td class="px-2 py-1">TOTAL BIAYA KEUANGAN I</td>
                                    <td class="px-2 py-1">{{ number_format($totalD , 2, ',', '.') }}</td>
                                </tr>
                                <tr class="border-t bg-gray-50">
                                    <td class="px-2 py-1">LABA/RUGI BERSIH SESUDAH PAJAK</td>
                                    
                                    <td class="px-2 py-1">{{ number_format($pajak = $bersih - $totalG , 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>

        
        </div>
    </x-keuangan.card-keuangan>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
    <script>
        function filterBulan(bulan) {
            const tahun = document.getElementById('tahun').value;
            window.location.href = `{{ route('laba-rugi.index') }}?bulan=${bulan}&tahun=${tahun}`;
        }

        function filterBulanAndYear() {
            const activeButton = document.querySelector('button.active');
            const bulan = activeButton ? activeButton.getAttribute('data-bulan') : 1; // Default to January if no active button
            const tahun = document.getElementById('tahun').value;
            window.location.href = `{{ route('laba-rugi.index') }}?bulan=${bulan}&tahun=${tahun}`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('button[data-bulan]');
            buttons.forEach(button => {
                button.addEventListener('click', function () {
                    buttons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    </script>

</x-Layout.layout>