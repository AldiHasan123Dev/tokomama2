<div class="table-container">
    <table class="table-cus">
        <thead>
            <tr>
                <th class="bg-thn" rowspan="3">Customer</th>
                <th class="bg-thn" colspan="{{ count($months) * 2 }}">Bulan</th>
                <th class="bg-total" rowspan="3">Total</th>
            </tr>
            <tr>
                @foreach ($months as $month)
                    <th class="text-center sticky-footer" colspan="2">{{ $month }}</th>
                @endforeach
            </tr>
            <tr>
                @foreach ($months as $month)
                    <th>Inv</th>
                    <th>Total</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($mergedResults as $customer_id => $customerData)
                @php $totalOmzet = 0; @endphp
                <tr>
                    <td class="bg-thn">{{ $customerData['customer_name'] }}</td>

                    @foreach ($months as $month)
                        @php
                            $data = $customerData['years'][$year][$month] ?? null;

                            $inv = isset($data['selisih_invoice']) ? (int) $data['selisih_invoice'] : 0;
                            $omzet = isset($data['omzet']) ? (int) $data['omzet'] : 0;

                            $totalOmzet += $omzet;

                            $warningClass = $inv === 0 && $omzet > 0 ? 'kurang-bayar' : '';
                        @endphp

                        <td class="text-center {{ $warningClass }}">
                            {{ $inv === 0 ? '-' : $inv }}
                        </td>
                        <td class="text-end {{ $warningClass }}">
                            {{ number_format($omzet, 0, ',', '.') }}
                        </td>
                    @endforeach

                    <td class="bg-total text-end">{{ number_format($totalOmzet, 0, ',', '.') }}</td>
                </tr>
            @endforeach

            {{-- Footer Total Per Bulan --}}
            <tr class="sticky-footer">
                <td class="bg-total-thn">{{ $year }}</td>
                @php
                    $totalYearlyOmzet = 0;
                @endphp
                @foreach ($months as $month)
                    @php
                        $monthlyOmzet = $monthlyTotals[$year][$month] ?? 0;
                        $invoiceCount = $monthlySelisihInvoice[$year][$month] ?? 0;
                        $totalYearlyOmzet += $monthlyOmzet;
                    @endphp
                    <td class="bg-total-monthly1 text-end">
                        {{ $invoiceCount == 0 ? '-' : $invoiceCount }}
                    </td>
                    <td class="bg-total-monthly text-end">{{ number_format($monthlyOmzet, 0, ',', '.') }}</td>
                @endforeach
                <td class="bg-total1 text-end">{{ number_format($totalYearlyOmzet, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>
