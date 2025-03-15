<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Report Omzet Cust</x-slot:tittle>

        <!-- Dropdown untuk memilih tahun -->
        <form action="{{ route('laporan.LOC') }}" method="GET">
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
        
        
        <style type="text/css">
        .table-container {
            overflow-x: auto;
            overflow-y: auto;
            max-height: 500px; /* Sesuaikan tinggi maksimum agar tidak terlalu panjang */
            margin-top: 20px;
            border: 1px solid #ddd;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 800px; /* Pastikan tabel tidak terlalu sempit */
            margin: 0 auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 12px;
        }

        th, td {
            padding: 8px 10px;
            border: 1px solid #ddd;
            transition: background-color 0.3s ease;
        }

        th {
            background-color: #7b7d80;
            color: white;
            position: sticky;
            top: 0;
            z-index: 2;
            border: 1px solid #ddd;
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
        }
        .sticky-footer {
    position: sticky;
    bottom: 0;
    background-color: rgb(0, 0, 0); /* Sesuaikan warna agar tidak menutupi konten */
    font-weight: bold;
    z-index: 2;
}


        .bg-total1 { background-color: #473f39; color: white; font-weight: bold; text-align: right; }
        .bg-total-thn { background-color: #0f2d44; color: white; font-weight: bold; }
        .bg-total-monthly { background-color: #098e0c;  color: white; font-weight: bold; text-align: right;}
        .bg-total { background-color: #93685b; color: white; font-weight: bold;}
        </style>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="bg-thn" rowspan="2">Customer</th>
                        <th class="bg-thn" colspan="{{ count($months) }}">Bulan</th>
                        <th class="bg-total">Total</th>
                    </tr>
                    <tr>
                        @foreach ($months as $month)
                            <th>{{ $month }}</th>
                        @endforeach
                        <th class="bg-total">Omzet Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($mergedResults as $customer_id => $customerData)
                    @php
                        $totalOmzet = 0;
                    @endphp
                    <tr>
                        <td class="bg-thn">{{ $customerData['customer_name'] }}</td>
                        @foreach ($months as $month)
                            @php
                                $data = $customerData['years'][request('year') ?? 2025][$month] ?? null;
                                $omzet = $data['omzet'] ?? 0;
                                $totalOmzet += $omzet;
                            @endphp
                            <td style="text-align: right">{{ number_format($omzet, 0, ',', '.') }}</td>
                        @endforeach
                        <td class="bg-total" style="text-align: right">{{ number_format($totalOmzet, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="sticky-footer">
                    <td class="bg-total-thn">{{ request('year') ?? 2025 }}</td>
                    @php $totalYearlyOmzet = 0; @endphp
                    @foreach ($months as $month)
                        @php
                            $monthlyOmzet = $monthlyTotals[request('year') ?? 2025][$month] ?? 0;
                            $totalYearlyOmzet += $monthlyOmzet;
                        @endphp
                        <td class="bg-total-monthly">{{ number_format($monthlyOmzet, 0, ',', '.') }}</td>
                    @endforeach
                    <td class="bg-total1">{{ number_format($totalYearlyOmzet, 0, ',', '.') }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</x-Layout.layout>
