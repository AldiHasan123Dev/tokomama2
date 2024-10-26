<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Laporan Omzet Total</x-slot:tittle>
        <style type="text/css">
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
                margin: 0 auto; /* Center the table */
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Add shadow for depth */
            }

            th, td {
                padding: 12px;
                text-align: center;
                transition: background-color 0.3s ease; /* Transition for background color */
            }

            th {
                background-color: #007bff;
                color: rgb(4, 3, 3);
                position: sticky;
                top: 0; /* Make the header sticky */
                z-index: 1; /* Ensure it stays above other elements */
            }

            tr:hover {
                background-color: #a0a0a0; /* Change background on hover */
            }

            /* Kelas untuk warna latar belakang setiap bulan */
            .bg-thn { background-color: #ca8c8c; }
            .bg-jan { background-color: #fce4ec; }
            .bg-feb { background-color: #f1f8e9; }
            .bg-mar { background-color: #e1f5fe; }
            .bg-apr { background-color: #fce4ec; }
            .bg-may { background-color: #f1f8e9; }
            .bg-jun { background-color: #e1f5fe; }
            .bg-jul { background-color: #fce4ec; }
            .bg-aug { background-color: #f1f8e9; }
            .bg-sep { background-color: #e1f5fe; }
            .bg-oct { background-color: #fce4ec; }
            .bg-nov { background-color: #f1f8e9; }
            .bg-dec { background-color: #e1f5fe; }
            .bg-total { background-color: #ffe0b2; }
        </style>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th class="bg-thn" rowspan="2">Tahun</th>
                        @foreach ($months as $index => $month)
                            @php
                                $bgClass = match ($index) {
                                    0 => 'bg-jan', 1 => 'bg-feb', 2 => 'bg-mar', 
                                    3 => 'bg-apr', 4 => 'bg-may', 5 => 'bg-jun', 
                                    6 => 'bg-jul', 7 => 'bg-aug', 8 => 'bg-sep', 
                                    9 => 'bg-oct', 10 => 'bg-nov', 11 => 'bg-dec',
                                    default => '',
                                };
                            @endphp
                            <th class="{{ $bgClass }}" colspan="4">{{ $month }}</th>
                        @endforeach
                        <th class="bg-total" colspan="5">Total</th>
                    </tr>
                    <tr>
                        @foreach ($months as $index => $month)
                            @php
                                $bgClass = match ($index) {
                                    0 => 'bg-jan', 1 => 'bg-feb', 2 => 'bg-mar', 
                                    3 => 'bg-apr', 4 => 'bg-may', 5 => 'bg-jun', 
                                    6 => 'bg-jul', 7 => 'bg-aug', 8 => 'bg-sep', 
                                    9 => 'bg-oct', 10 => 'bg-nov', 11 => 'bg-dec',
                                    default => '',
                                };
                            @endphp
                            <th class="{{ $bgClass }}">Q.Inv</th>
                            <th class="{{ $bgClass }}">Beli</th>
                            <th class="{{ $bgClass }}">Jual</th>
                            <th class="{{ $bgClass }}">Profit</th>
                        @endforeach
                        <th class="bg-total">Q.Inv</th>
                        <th class="bg-total">Beli</th>
                        <th class="bg-total">Jual</th>
                        <th class="bg-total">Profit</th>
                        <th class="bg-total">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoiceData as $year => $dataPerYear)
                        <tr>
                            <td class="bg-thn">{{ $year }}</td>
                            @foreach ($months as $index => $month)
                                @php
                                    $data = collect($dataPerYear)->firstWhere('month', $month);
                                    $bgClass = match ($index) {
                                        0 => 'bg-jan', 1 => 'bg-feb', 2 => 'bg-mar', 
                                        3 => 'bg-apr', 4 => 'bg-may', 5 => 'bg-jun', 
                                        6 => 'bg-jul', 7 => 'bg-aug', 8 => 'bg-sep', 
                                        9 => 'bg-oct', 10 => 'bg-nov', 11 => 'bg-dec',
                                        default => '',
                                    };
                                @endphp
                                <td class="{{ $bgClass }}">{{ $data['invoice_count'] ?? 0 }}</td>
                                <td class="{{ $bgClass }}">
                                    {{ number_format($data['total_harga_beli'] ?? 0, 0, ',', ',') }}</td>
                                <td class="{{ $bgClass }}">
                                    {{ number_format($data['total_harga_jual'] ?? 0, 0, ',', ',') }}</td>
                                <td class="{{ $bgClass }}">
                                    {{ number_format($data['total_profit'] ?? 0, 0, ',', ',') }}</td>
                            @endforeach
                            <td class="bg-total">{{ $summaryData[$year]['total_invoice_count'] ?? 0 }}</td>
                            <td class="bg-total">
                                {{ number_format($summaryData[$year]['total_harga_beli'] ?? 0, 0, ',', ',') }}</td>
                            <td class="bg-total">
                                {{ number_format($summaryData[$year]['total_harga_jual'] ?? 0, 0, ',', ',') }}</td>
                            <td class="bg-total">
                                {{ number_format($summaryData[$year]['total_profit'] ?? 0, 0, ',', ',') }}</td>
                            <td class="bg-total">
                                {{ number_format(round($summaryData[$year]['total_profit_percentage'] ?? 0, 1), 2, ',', '.') }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</x-Layout.layout>
