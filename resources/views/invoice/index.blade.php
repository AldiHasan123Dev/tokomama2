<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Pengambilan Nomor Faktur Untuk Invoice</x-slot:tittle>
        <form action="{{ route('preview.invoice') }}" method="post" id="form">
            @csrf
            
            <input type="date" name="tgl_invoice" value="{{ date('Y-m-d') }}">
            <input type="text" value="{{ $no_JNL }}/SB/{{ date('y') }}" name="tipe" readonly>
            <input type="hidden" name="invoice_count" value="{{ $invoice_count }}">
            
            @foreach ($transaksi as $item)
            <div style="overflow-x: auto; margin-top: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                <table style="border-collapse: collapse; width: 100%; margin: 0 auto;">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">#</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Nama Barang</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Jumlah Barang</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Harga Satuan</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-{{ $item->id }}">
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">1</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $item->barang->nama }}</td>
                            <td hidden class="invoice-{{ $item->id }}">
                                <select hidden name="invoice[{{ $item->id }}][]" class="select w-full">
                                    @for ($i = 0; $i < $invoice_count; $i++)
                                        <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>Invoice Ke - {{ $i + 1 }}</option>
                                    @endfor
                                </select>
                            </td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                <input onclick="this.select()" id="qty-{{ $item->id }}-1" type="number" onchange="inputBarang({{ $item->id }}, this.value,{{ $item->harga_jual }}, {{ $item->jumlah_jual }})" name="jumlah[{{ $item->id }}][]" value="{{ $item->sisa }}">
                            </td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ number_format($item->harga_jual) }}</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;" id="total-{{ $item->id }}-1">{{ number_format($item->harga_jual * $item->jumlah_jual) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" style="text-align: center;">
                                <button onclick="addRow({{ $item->id }}, {{ $item->harga_jual }}, {{$item->jumlah_jual}},'{{ $item->barang->nama }}')" type="button"  class="btn bg-orange-500 font-semibold justify-align-center text-white w-full mt-3">Tambah Kolom</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endforeach

            <button  class="btn bg-green-500 font-semibold justify-align-center text-white w-full mt-3" type="submit">Preview Invoice</button>
        </form>
    </x-keuangan.card-keuangan>

    <x-slot:script>
    <script>
        let idx = 1;
        let ids = @json($ids);
        let array_jumlah = @json($array_jumlah);
        array_jumlah = JSON.parse(array_jumlah);

        function inputBarang(id, value, price, max) {
            var $j_object = $(".qty-" + id);
            let sum = 0;
            $j_object.each( function(){
                sum += parseInt($(this).val());
            });

            if (sum > max) {
                alert('Jumlah melebihi batas');
                return;
            }

            let total = parseFloat(value) * parseInt(price);
            $('#total-' + id + '-' + idx).html(total);
        }

        function addRow(id, price, max, barang) {
            idx++;
            let html = `<tr>
                        <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">${idx}</td>
                        <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">${barang}</td>
                        <td class="invoice-${id}" style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                            <select name="invoice[${id}][]" class="select w-full">
                                @for ($i = 0; $i < $invoice_count; $i++)
                                    <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>Invoice Ke - {{ $i + 1 }}</option>
                                @endfor
                            </select>
                        </td>
                        <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                            <input onclick="this.select()" id="qty-${id}-${idx}" type="number" onchange="inputBarang(${id}, this.value, ${price}, ${max})" name="jumlah[${id}][]" value="0">
                        </td>
                        <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">${price}</td>
                        <td id="total-${id}-${idx}" style="border: 1px solid #ddd; padding: 12px; text-align: center;"></td>
                    </tr>`;

            let valueNow = $(`#qty-${id}-${idx}`).val();
            let data = [];
            for (var i = idx; i > 0; i--) {
                let valueAbove = $(`#qty-${id}-${i}`).val();
                data.push(valueAbove);
            }

            let sum = data.slice(1).reduce((accumulator, currentValue) => parseInt(accumulator) + parseInt(currentValue), 0);
            if (sum == max || sum > max) {
                alert("Kuantitas melebihi batas");
                idx--;
                return;
            }

            $(`#qty-${id}-${idx}`).on({
                change: function () {
                    let valueAbove = $(`#qty-${id}-${idx - 1}`).val();
                    var inputValue = $(this).val();
                    if (parseInt(inputValue) + parseInt(sum) > max) {
                        alert('Total quantity tidak sama dengan jumlah yang di jual');
                        $(this).val(0);
                        return; // inputan tetap muncul
                    }
                }
            });

            $('#tbody-' + id).append(html);
        }

        function invoice_counts() {
            let val = $('#invoice_count').val();
            $.each(ids, function (indexInArray, item) {
                let options = '<option selected>Pilih Invoice</option>';
                for (let i = 1; i <= val; i++) {
                    options += `<option value="${i}">Invoice Ke - ${i}</option>`;
                }
                let html = `<select name="invoice[${item}][]" class="px-2 form-control">${options}</select>`;
                $('.invoice-' + item).html(html);
            });
        }

        $('#form').submit(function (e) { 
            e.preventDefault();
            for (let i = 0; i < ids.length; i++) {
                const id = ids[i];
                var $j_object = $(".qty-" + id);
                let sum = 0;
                $j_object.each(function() {
                    sum += parseInt($(this).val());
                });

                if (sum > array_jumlah[id]) {
                    alert('Jumlah melebihi batas');
                    return;
                } else {
                    this.submit();
                }
            }
        });
    </script>
    </x-slot:script>
</x-Layout.layout>
