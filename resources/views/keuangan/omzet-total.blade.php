<x-Layout.layout>
    <!-- jQuery -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"
        integrity="sha512-ELV+xyi8IhEApPS/pSj66+Jiw+sOT1Mqkzlh8ExXihe4zfqbWkxPRi8wptXIO9g73FSlhmquFlUOuMSoXz5IRw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="{{ asset('assets/css/ui.jqgrid-bootstrap5.css') }}" />

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Laporan Omzet Total</x-slot:tittle>
        <style type="text/css">
            /* Tambahan gaya jika diperlukan */
            body {
                background-color: #f9f9f9; /* Warna latar belakang halaman */
            }

            .table-container {
                overflow-x: auto; /* Mengaktifkan scroll horizontal */
                margin-top: 20px; /* Jarak atas jika perlu */
            }

            .tg {
                border-collapse: collapse;
                width: 100%;
            }

            .tg td,
            .tg th {
                border: 1px solid #ddd; /* Border yang lebih halus */
                padding: 12px; /* Padding lebih banyak */
            }

            /* Kelas untuk warna latar belakang setiap bulan */
            .bg-thn { background-color: #e3f2fd; }   /* Juni */
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
            .bg-total { background-color: #ffe0b2; }   /* Oktober */
        </style>

        <div class="table-container">
            <table class="table table-bordered table-hover table-striped">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center bg-thn" rowspan="2">Tahun</th>
                        @foreach ($months as $index => $month)
                            @php
                                // Menentukan kelas berdasarkan bulan untuk header
                                $bgClass = match ($index) {
                                    0 => 'bg-jan',   // Januari
                                    1 => 'bg-feb',   // Februari
                                    2 => 'bg-mar',   // Maret
                                    3 => 'bg-apr',   // April
                                    4 => 'bg-may',   // Mei
                                    5 => 'bg-jun',   // Juni
                                    6 => 'bg-jul',   // Juli
                                    7 => 'bg-aug',   // Agustus
                                    8 => 'bg-sep',   // September
                                    9 => 'bg-oct',   // Oktober
                                    10 => 'bg-nov',  // November
                                    11 => 'bg-dec',  // Desember
                                    default => ''    // Default jika tidak ada
                                };
                            @endphp
                            <th class="text-center {{ $bgClass }}" colspan="4">{{ $month }}</th>
                        @endforeach
                        <th class="text-center  bg-total" colspan="4">Total</th>
                    </tr>
                    <tr>
                        @foreach ($months as $index => $month)
                            @php
                                // Menentukan kelas berdasarkan bulan untuk header sub
                                $bgClass = match ($index) {
                                    0 => 'bg-jan',   // Januari
                                    1 => 'bg-feb',   // Februari
                                    2 => 'bg-mar',   // Maret
                                    3 => 'bg-apr',   // April
                                    4 => 'bg-may',   // Mei
                                    5 => 'bg-jun',   // Juni
                                    6 => 'bg-jul',   // Juli
                                    7 => 'bg-aug',   // Agustus
                                    8 => 'bg-sep',   // September
                                    9 => 'bg-oct',   // Oktober
                                    10 => 'bg-nov',  // November
                                    11 => 'bg-dec',  // Desember
                                    default => ''    // Default jika tidak ada
                                };
                            @endphp
                            <th class="text-center {{ $bgClass }}">Jumlah Inv</th>
                            <th class="text-center {{ $bgClass }}">Beli</th>
                            <th class="text-center {{ $bgClass }}">Jual</th>
                            <th class="text-center {{ $bgClass }}">Profit</th>
                        @endforeach
                        <th class="text-center  bg-total">Jumlah Inv</th>
                        <th class="text-center  bg-total">Beli</th>
                        <th class="text-center  bg-total">Jual</th>
                        <th class="text-center  bg-total">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoiceData as $year => $dataPerYear)
                        <tr>
                            <td class="text-center bg-thn">{{ $year }}</td>
                            @foreach ($months as $index => $month)
                                @php
                                    // Mencari data untuk bulan tertentu
                                    $data = collect($dataPerYear)->firstWhere('month', $month);
                                    // Menentukan kelas berdasarkan bulan untuk data
                                    $bgClass = match ($index) {
                                        0 => 'bg-jan',   // Januari
                                        1 => 'bg-feb',   // Februari
                                        2 => 'bg-mar',   // Maret
                                        3 => 'bg-apr',   // April
                                        4 => 'bg-may',   // Mei
                                        5 => 'bg-jun',   // Juni
                                        6 => 'bg-jul',   // Juli
                                        7 => 'bg-aug',   // Agustus
                                        8 => 'bg-sep',   // September
                                        9 => 'bg-oct',   // Oktober
                                        10 => 'bg-nov',  // November
                                        11 => 'bg-dec',  // Desember
                                        default => ''    // Default jika tidak ada
                                    };
                                @endphp
                                <td class="text-center {{ $bgClass }}">{{ $data['invoice_count'] ?? 0 }}</td>
                                <td class="text-center {{ $bgClass }}">{{ number_format($data['total_harga_beli'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center {{ $bgClass }}">{{ number_format($data['total_harga_jual'] ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center {{ $bgClass }}">{{ number_format($data['total_profit'] ?? 0, 0, ',', '.') }}</td>
                            @endforeach
                           
                            <!-- Total per tahun -->
                            <td class="text-center  bg-total">{{ $summaryData[$year]['total_invoice_count'] ?? 0 }}</td>
                            <td class="text-center  bg-total">{{ number_format($summaryData[$year]['total_harga_beli'] ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center  bg-total">{{ number_format($summaryData[$year]['total_harga_jual'] ?? 0, 0, ',', '.') }}</td>
                            <td class="text-center bg-total">{{ number_format($summaryData[$year]['total_profit'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script>
            // Tambahan script jika diperlukan
        </script>
    </x-slot:script>
</x-Layout.layout>
