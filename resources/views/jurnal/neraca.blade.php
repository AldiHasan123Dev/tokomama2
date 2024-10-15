<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Laporan Neraca</x-slot:tittle>
        <div class="overflow-x-auto">
            @php
                $namaBulan = [
                    1 => 'Januari', 
                    2 => 'Februari', 
                    3 => 'Maret', 
                    4 => 'April', 
                    5 => 'Mei', 
                    6 => 'Juni', 
                    7 => 'Juli', 
                    8 => 'Agustus', 
                    9 => 'September', 
                    10 => 'Oktober', 
                    11 => 'November', 
                    12 => 'Desember'
                ];
            @endphp

            <div>
                <div class="flex justify-between">
                    <a href="#" target="_blank"
                        class="btn bg-green-400 text-white my-5 py-4 font-bold" id="print" onclick="window.print()">
                        <i class="fas fa-print"></i> Print Laporan
                    </a>
                </div>
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
                <b class="mr-2 margin-top:40px" style="padding-left : 80px;">Tahun:</b>
                <select class="form-control-bordered rounded-lg dark:text-black my-7 py-3 w-24 pl-2" id="tahun" onchange="filterBulanAndYear()">
                    @for($year = 2024; $year <= 2030; $year++)
                        <option value="{{ $year }}" {{ $year == $tahun ? 'selected' : '' }}>{{ $year }}</option>
                    @endfor
                </select>           
            </div>   
        </div>
        <div class="w-full flex justify-center text-center">
            <h1>Laporan Neraca Per {{ $namaBulan[$bulan] }} {{ $tahun }}</h1><br>
        </div> 

        <div class="flex justify-between mb-10">
            <div class="w-5/12 ml-10">
                <table class="table-auto w-full text-xs">
                    <thead>
                        <tr>
                            <th colspan="3" class="px-4 py-2 text-left">AKTIVA LANCAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coa1 as $item)
                            @php
                                $total = $totals[$item->id] ?? ['selisih' => 0];
                            @endphp
                            <tr>
                                <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL AKTIVA LANCAR</td>
                            <td class="border px-4 py-2 font-bold text-right">{{ number_format($totalA, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table-auto w-full text-xs mt-5">
                    <thead>
                        <tr>
                            <th colspan="3" class="px-4 py-2 text-left">AKTIVA TAK LANCAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coa2 as $item)
                            @php
                                $total = $totals[$item->id] ?? ['selisih' => 0];
                            @endphp
                            <tr>
                                <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL AKTIVA TAK LANCAR</td>
                            <td class="border px-4 py-2 font-bold text-right">{{ number_format($totalB, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="w-5/12 mr-10">
                <table class="table-auto w-full text-xs">
                    <thead>
                        <tr>
                            <th colspan="3" class="px-4 py-2 text-left">PASIVA</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coa3 as $item)
                            @php
                                $total = $totals[$item->id] ?? ['selisih' => 0];
                            @endphp
                            <tr>
                                <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL PASIVA</td>
                            <td class="border px-4 py-2 font-bold text-right">{{ number_format($totalC, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table-auto w-full text-xs mt-5">
                    <thead>
                        <tr>
                            <th colspan="3" class="px-4 py-2 text-left">MODAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coa4 as $item)
                            @php
                                $total = $totals[$item->id] ?? ['selisih' => 0];
                            @endphp
                            <tr>
                                <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                <td class="border px-4 py-2">{{ $item->nama_akun }}</td>                               
                                <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" class="border px-4 py-2 font-bold">TOTAL MODAL</td>
                            <td class="border px-4 py-2 font-bold text-right">{{ number_format($totalD, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-between">
            <table class="table-auto w-full border black px-2 py-2 text-center mb-20">
             <tr>
                    <th>TOTAL AKTIVA</th>
                      <th>TOTAL PASIVA</th>
                </tr>
               <tr> 
                 <td>{{ number_format($aktiva = $totalA + $totalB, 2, ',', '.') }}</td>
                     <td>{{ number_format($pasiva = $totalC + $totalD, 2, ',', '.') }}</td>
                 </tr>       
            </table>
        </div>
    </x-keuangan.card-keuangan>
</x-Layout.layout>

<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/select/2.0.3/js/dataTables.select.js"></script>
<script>
    function filterBulan(bulan) {
        const tahun = document.getElementById('tahun').value;
        const url = new URL(window.location.href);
        url.searchParams.set('bulan', bulan);
        url.searchParams.set('tahun', tahun);
        window.location.href = url.toString();
    }

    function filterBulanAndYear() {
        const bulan = document.querySelector('[data-bulan].bg-green-600').getAttribute('data-bulan');
        filterBulan(bulan);
    }
</script>
