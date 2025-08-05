<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Dashboard</x-slot:tittle>

        @php
            $totalSaldoA = $coa1->sum(fn($item) => $totals[$item->id]['selisih'] ?? 0);
            $totalSaldoB = $coa2->sum(fn($item) => $totals[$item->id]['selisih'] ?? 0);
        @endphp

        <div class="flex justify-center gap-10 mb-10 px-10 mt-5">
            <!-- Tabel COA 1 -->
            <div class="w-1/2">
                <table class="table-auto w-full text-xs border border-gray-300 rounded shadow-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">No Akun</th>
                            <th class="border px-4 py-2 text-left">Nama Akun</th>
                            <th class="border px-4 py-2 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coa1 as $item)
                            @php
                                $total = $totals[$item->id] ?? ['selisih' => 0];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="2" class="border px-4 py-2 text-left">TOTAL</td>
                            <td class="border px-4 py-2 text-right text-green-700">{{ number_format($totalSaldoA, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Tabel COA 2 -->
            <div class="w-1/2">
                <table class="table-auto w-full text-xs border border-gray-300 rounded shadow-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-4 py-2 text-left">No Akun</th>
                            <th class="border px-4 py-2 text-left">Nama Akun</th>
                            <th class="border px-4 py-2 text-right">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coa2 as $item)
                            @php
                                $total = $totals[$item->id] ?? ['selisih' => 0];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="border px-4 py-2">{{ $item->no_akun }}</td>
                                <td class="border px-4 py-2">{{ $item->nama_akun }}</td>
                                <td class="border px-4 py-2 text-right">{{ number_format($total['selisih'], 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-100 font-bold">
                        <tr>
                            <td colspan="2" class="border px-4 py-2 text-left">TOTAL</td>
                            <td class="border px-4 py-2 text-right text-green-700">{{ number_format($totalSaldoB, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </x-keuangan.card-keuangan>
</x-Layout.layout>
