<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Laporan Hutang Vendor</x-slot:tittle>
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
            .bg-thn { background-color: #8c9eca; }
.bg-jan { background-color: #d1c4e9 }
.bg-feb { background-color: #ffccbc; }
.bg-mar { background-color: #ffe0b2; }
.bg-apr { background-color: #d1c4e9; }
.bg-may { background-color: #c8e6c9; }
.bg-jun { background-color: #d1c4e9; }
.bg-jul { background-color: #ffecb3; }
.bg-aug { background-color: #c5cae9; }
.bg-sep { background-color: #b2dfdb; }
.bg-oct { background-color: #f3e5f5; }
.bg-nov { background-color: #fff9c4; }
.bg-dec { background-color: #cfd8dc; }
.bg-total { background-color: #ffab91; }

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
                            <th class="{{ $bgClass }}">Piutang</th>
                            <th class="{{ $bgClass }}">Lunas</th>
                            <th class="{{ $bgClass }}">Belum</th>
                        @endforeach
                        <th class="bg-total">Q.Inv</th>
                        <th class="bg-total">Piutang</th>
                        <th class="bg-total">Lunas</th>
                        <th class="bg-total">Belum</th>
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
                                    {{ number_format($data['total_piutang'] ?? 0, 0, ',', ',') }}</td>
                                <td class="{{ $bgClass }}">
                                    {{ number_format($data['total_lunas'] ?? 0, 0, ',', ',') }}</td>
                                <td class="{{ $bgClass }}">
                                   0</td>

                            @endforeach
                            <td class="bg-total">{{ $summaryData[$year]['total_invoice_count'] ?? 0 }}</td>
                            <td class="bg-total">
                                {{ number_format($summaryData[$year]['total_piutang'] ?? 0, 0, ',', ',') ?? 0 }}</td>
                            <td class="bg-total">
                                {{ number_format($summaryData[$year]['total_lunas'] ?? 0, 0, ',', ',') ?? 0 }}</td>
                            <td class="bg-total">
                                0</td>
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
