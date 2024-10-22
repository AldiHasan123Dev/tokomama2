<x-Layout.layout>
    <style>
        tr.selected{
            background-color: lightskyblue !important;
        }
    </style>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Pengambilan Nomor Faktur Untuk Invoice</x-slot:tittle>
        <form action="{{ route('preview.invoice') }}" method="post" id="form">
            @csrf
            
            <input type="date" name="tgl_invoice" value="{{ date('Y-m-d') }}">
            <input type="text" value="{{ $no_JNL }}/SB/{{ date('y') }}" name="tipe" readonly>
            <input type="hidden" name="invoice_count" value="{{ $invoice_count }}">
            {{-- <label for="invoice_count">Masukan Jumlah Invoice</label>
            <input type="number" onchange="invoice_counts()" onkeyup="invoice_counts()" name="invoice_count" id="invoice_count" min="1" value="1" class="form-control w-full text-center rounded-sm"> --}}
            @foreach ($transaksi as $item)
            <div class="overflow-x-auto mt-5 shadow-lg">
                <table class="table" id="table-getfaktur">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th>Invoice</th>
                            <th>Jumlah Barang ({{ $item->sisa }})</th>
                            <th>Harga Satuan</th>
                            <th>Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-{{ $item->id }}">
                        <tr>
                            <td>1</td>
                            <td>{{ $item->barang->nama }}</td>
                            <td hidden class="invoice-{{ $item->id }}">
                                <select hidden name="invoice[{{ $item->id }}][]" class="select w-full" id="">
                                    @for ($i = 0; $i < $invoice_count; $i++)
                                        <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>Invoice Ke - {{ $i + 1 }}</option>
                                    @endfor
                                </select>
                            </td>

                            <!-- inputan quantity invoice -->
                            <td><input onclick="this.select()" id="qty-{{ $item->id }}-1" class="qty-{{ $item->id }}" type="number" class="rounded-sm" onchange="inputBarang({{ $item->id }}, this.value,{{ $item->harga_jual }}, {{ $item->jumlah_jual }})" name="jumlah[{{ $item->id }}][]" id="jumlah" value="{{ $item->sisa }}"></td>

                            <!-- harga satuan -->
                            <td>{{ number_format($item->harga_jual) }}</td>

                            <!-- total -->
                            <td id="total-{{ $item->id }}-1">{{ number_format($item->harga_jual * $item->jumlah_jual) }}</td>

                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7">
                                <button onclick="addRow({{ $item->id }}, {{ $item->harga_jual }}, {{$item->jumlah_jual}},'{{ $item->barang->nama }}')" type="button" class="btn bg-yellow-400 btn-sm w-full">Tambah Kolom</button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endforeach

        {{-- <button class="btn bg-green-500 font-semibold text-white w-full mt-3" type="submit" onclick="return confirm('Submit Invoice?')">Submit Invoice</button> --}}
        <button class="btn bg-yellow-500 font-semibold text-white w-full mt-3" type="submit">Preview Invoice</button>
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
                sum+=parseInt($(this).val());
            });

            if (sum > max) {
                alert('Jumlah melebihi batas');
                return
            }
            let total = parseFloat(value) * parseInt(price);

            $('#total-' + id + '-'+idx ).html(total);
        }

        function addRow(id, price, max, barang){
            idx++;
            let html = `<tr>
                        <td>${idx}</td>
                        <td>${barang}</td>
                        <td class="invoice-${id}">
                            <select name="invoice[${id}][]" class="select w-full" id="">
                                @for ($i = 0; $i < $invoice_count; $i++)
                                    <option value="{{ $i }}" {{ $i == 0 ? 'selected' : '' }}>Invoice Ke - {{ $i + 1 }}</option>
                                @endfor
                            </select>
                        </td>

                        <td>
                            <input onclick="this.select()" id="qty-${id}-${idx}" type="number" class="qty-${id}" onchange="inputBarang(${id}, this.value,${price}, ${max})" name="jumlah[${id}][]" id="jumlah" value="0">
                        </td>

                        <td>${price}</td>
                        <td id="total-${id}-${idx}"></td>
                    </tr>`;


            let valueNow = $(`#qty-${id}-${idx}`).val();

            let data = [];
            for(var i = idx; i > 0; i--) {
                let valueAbove = $(`#qty-${id}-${i}`).val();
                data.push(valueAbove);
            }

            let sum = data.slice(1).reduce((accumulator, currentValue) => parseInt(accumulator) + parseInt(currentValue), 0);

            if(sum == max || sum > max) {
                alert("Kuantitas melebihi batas");
                idx--;
                return
            }

            $(`#qty-${id}-${idx}`).on({
                change: function () {
                    let valueAbove = $(`#qty-${id}-${idx - 1}`).val();
                    var inputValue = $(this).val();
                    if(parseInt(inputValue) + parseInt(sum) > max) {
                        alert('Total quantity tidak sama dengan jumlah yang di jual');
                        var inputValue = $(this).val(0);
                        return // inputan tetap muncul
                    }
                }
            });

            $('#tbody-' + id).append(html);

        }

        function invoice_counts(){
            let val = $('#invoice_count').val();
            $.each(ids, function (indexInArray, item) {
                let options = '<option selected>Pilih Invoice</option>';
                for(let i = 1; i <= val; i++){
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
                $j_object.each( function(){
                    sum+=parseInt($(this).val());
                });

                if (sum > array_jumlah[id]) {
                    alert('Jumlah melebihi batas');
                    return
                }else{
                    this.submit();
                }
            }
        });
    </script>
    </x-slot:script>
</x-Layout.layout>
