<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Report Sales</x-slot:tittle>

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
        .form-container {  /* Memposisikan elemen secara absolut */           /* Menjauhkan dari atas halaman */
    right: 90px;         /* Memposisikan form ke kanan */       /* Pastikan elemen berada di atas konten lainnya */
}

.mb-3 {
    margin-right: 10px;
}

.input-group {
    display: flex; /* Membuat select dan tombol berada di dalam satu baris */
}

.form-select {
    margin-right: 5px; /* Jarak antara select dan button */
}

.btn-primary {
    white-space: nowrap; /* Menghindari tombol terpotong jika teks terlalu panjang */
}

            body {
                background-color: #f9f9f9;
            }

            .table-container {
                overflow-x: auto;
                margin-top: 20px;
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
            }

            th {
                background-color: #7b7d80;
                color: white;
                position: sticky;
                top: 0;
                z-index: 1;
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
            
            .bg-total { background-color: #93685b; color: white; font-weight: bold;}
        </style>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="bg-thn" rowspan="2">Sales</th>
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
                        <td class="bg-thn">{{ $customerData['sales_name'] }}</td>
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
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</x-Layout.layout>
