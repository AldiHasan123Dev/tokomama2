<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Report Fee Sales</x-slot:tittle>

        <form action="{{ route('laporan.FLS') }}" method="GET">
            <div class="form-container">
                <div class="mb-3 mt-3">
                    <label for="year" class="form-label">Pilih Tahun</label>
                    <div class="input-group">
                        <select name="year" id="year" class="form-select">
                            <option value="" disabled selected>Pilih Tahun</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </div>
        </form>
        
        <style>
            th.bg-total-thn, td.bg-thn {
                position: sticky;
                left: 0;
                background-color: #6e791c;
                z-index: 2;
            }
            td.bg-total-thn {
                position: sticky;
                left: 0;
                background-color: #44390f;
                z-index: 2;
            }
            th.bg-total-thn {
                background-color: #44390f;
                z-index: 3;
            }
            .table-container {
                overflow-x: auto;
                white-space: nowrap;
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin: 0 auto;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                font-size: 12px;
            }
            th, td {
                padding: 8px 10px;
                border: 1px solid #ddd;
                transition: background-color 0.3s ease;
                text-align: right;
            }
            th {
                background-color: #294774;
                color: white;
                position: sticky;
                top: 0;
                z-index: 1;
            }
            td {
                background-color: #f9f9f9;
            }
            tr:hover {
                background-color: #f1f1f1;
            }
            .bg-thn {
                background-color: #6e791c;
                font-weight: bold;
                color: white;
                text-align: center;
            }
            .bg-total-thn {
                background-color: #44390f;
                color: white;
                font-weight: bold;
                text-align: center;
                border: 2px solid rgb(255, 255, 255);
            }
            .bg-total-monthly {
                background-color: #f7c842;
                font-weight: bold;
                text-align: right;
                border: 2px solid rgb(218, 207, 207);
            }
            /* Warna berbeda untuk setiap bulan */
            .month-1 { background-color: #d9a571 }
            .month-2 { background-color: #d2d23a; }
            .month-3 { background-color: #d9a571 }
            .month-4 { background-color: #d2d23a; }
            .month-5 { background-color: #d9a571 }
            .month-6 { background-color: #d2d23a; }
            .month-7 { background-color: #d9a571 }
            .month-8 { background-color: #d2d23a; }
            .month-9 { background-color: #d9a571; }
            .month-10 { background-color:#d2d23a; }
            .month-11 { background-color:#d9a571 }
            .month-12 { background-color:#d2d23a;; }
        </style>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="bg-total-thn" rowspan="2">Sales</th>
                        @foreach ($months as $index => $month)
                            <th colspan="{{ count($satuan) }}" class="month-{{ $index + 1 }}" style="text-align: center;">{{ $month }}</th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach ($months as $index => $month)
                            @foreach ($satuan as $s)
                                <th class="month-{{ $index + 1 }}" style="text-align: center;">{{ $s }}</th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                
                <tbody>
                    @foreach ($mergedResults as $customer_sales => $customerData)
                        <tr>
                            <td class="bg-thn">{{ $customerData['sales_name'] }}</td>
                            @foreach ($months as $index => $month)
                                @foreach ($satuan as $s)
                                    @php
                                        $data = $customerData['years'][request('year') ?? 2025][$month] ?? null;
                                        $omzet = $data[$s] ?? '-';
                                    @endphp
                                    <td class="month-{{ $index + 1 }}">{{ $omzet ?? '-' }}</td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                    {{-- <tr>
                        <td class="bg-total-thn">Total {{ request('year') ?? 2025 }}</td>
                        @foreach ($months as $month)
                            @foreach ($satuan as $satuanBarang)
                                @php
                                    // Ambil total omzet berdasarkan tahun, bulan, dan satuan barang
                                    $monthlyOmzet = $monthlyTotals[request('year') ?? 2025][$month][$satuanBarang] ?? 0;
                                @endphp
                                <td class="bg-total-monthly">{{ $monthlyOmzet ?? 0 }}</td>
                            @endforeach
                        @endforeach
                    </tr>                     --}}
                </tbody>
                
            </table>
        </div>        
    </x-keuangan.card-keuangan>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</x-Layout.layout>