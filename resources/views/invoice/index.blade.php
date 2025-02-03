<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <x-slot:tittle>Pengambilan Nomor Faktur Untuk Invoice</x-slot:tittle>
        <form action="{{ route('preview.invoice') }}" method="post" id="form">
            @csrf
            
            <input type="date" name="tgl_invoice" value="{{ date('Y-m-d') }}">
            <input type="text" value="{{ $no_JNL }}/SB/{{ date('y') }}" name="tipe" readonly>
            <input type="hidden" name="invoice_count" value="{{ $invoice_count }}">
            
            <div style="overflow-x: auto; margin-top: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                <table style="border-collapse: collapse; width: 100%; margin: 0 auto;">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">#</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Nama Barang</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Jumlah Barang</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Harga Beli Satuan</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Harga Jual Satuan</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                         @foreach ($transaksi as $item)
                        <tr>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $loop->iteration }}</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $item->barang->nama }}</td>
                            <td hidden style="border: 1px solid #ddd; padding: 12px; text-align: center;" class="invoice-{{ $item->id }}">
                                <select hidden name="invoice[{{ $item->id }}][]" class="select w-full">
                                    <option value="1" selected>Invoice Ke - 1</option>
                                    @for ($i = 2; $i <= $loop->index + 1; $i++)
                                        <option value="{{ $i }}" {{ $i == $loop->index + 1 ? 'selected' : '' }}>Invoice Ke - {{ $i }}</option>
                                    @endfor
                                </select>
                            </td>                       
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                <input onclick="this.select()" id="qty-{{ $item->id }}-1" type="number" onchange="inputBarang({{ $item->id }}, this.value,{{ $item->harga_jual }}, {{ $item->jumlah_jual }})" name="jumlah[{{ $item->id }}][]" value="{{ $item->sisa }}">
                            </td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: end;" id="harga_beli-{{ $item->id }}-1">{{ number_format($item->harga_beli) }}</td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: end;">
                                <input onclick="this.select()" id="price-{{ $item->id }}-1" type="number"  onchange="inputBarang({{ $item->id }}, $('#qty-{{ $item->id }}-1').val(), this.value, {{ $item->jumlah_jual }})" name="harga_jual[{{ $item->id }}][]" value="{{ $item->harga_jual }}">
                            </td>                            
                            <td style="border: 1px solid #ddd; padding: 12px; text-align:end" id="total-{{ $item->id }}-1">{{ number_format($item->harga_jual * $item->jumlah_jual) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            {{-- <td colspan="6" style="text-align: center;">
                                <button 
                                    onclick="addRow({{ $item->id }}, {{ $item->harga_beli }}, {{ $item->harga_jual }}, {{ $item->jumlah_jual }}, '{{ addslashes($item->barang->nama) }}')" 
                                    type="button" 
                                    class="btn bg-orange-500 font-semibold text-white w-full mt-3">
                                    Tambah Kolom
                                </button>
                            </td>                             --}}
                        </tr>
                    </tfoot>
                </table>
            </div>

            <button  class="btn bg-green-500 font-semibold justify-align-center text-white w-full mt-3" type="submit">Preview Invoice</button>
        </form>
    </x-keuangan.card-keuangan>

    <x-slot:script>
    <script>
         function formatRibuan(input) {
                let angka = input.value.replace(/,/g, ''); // Hapus koma
                input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Format dengan koma
            }

            function getCleanNumber(value) {
                // Ganti koma dengan kosong dan titik terakhir dengan koma
                const cleanValue = value.replace(/,/g, '').replace(/\.(?=.*\.)/g, ''); // Hapus koma
                const decimalIndex = value.lastIndexOf('.');
                if (decimalIndex !== -1) {
                    return parseFloat(cleanValue); // Menggunakan parseFloat untuk angka desimal
                }
                return parseInt(cleanValue) || 0; // Ubah ke integer, default 0 jika NaN
            }
        let idx = 1;
        let ids = @json($ids);
        let array_jumlah = @json($array_jumlah);
        array_jumlah = JSON.parse(array_jumlah);

        function inputBarang(id, qty, price, max) {
    var $j_object = $(".qty-" + id);
    let sum = 0;

    // Hitung total kuantitas barang
    $j_object.each(function() {
        sum += parseInt($(this).val());
    });

    if (sum > max) {
        alert('Jumlah melebihi batas');
        return;
    }

    // Hitung total harga
    let total = parseFloat(qty) * parseFloat(price);
    $('#total-' + id + '-' + idx).html(total.toLocaleString('id-ID'));
}


function addRow(id, harga_beli, price, max, barang) {
    idx++;
    let html = `
        <tr>
            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">${idx}</td>
            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">${barang}</td>
            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                <input onclick="this.select()" id="qty-${id}-${idx}" type="number" onchange="inputBarang(${id}, this.value, ${price}, ${max})" name="jumlah[${id}][]" value="0">
            </td>
            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;" id="harga_beli-${id}-${idx}">${harga_beli}</td>
            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                <input onclick="this.select()" id="price-${id}-${idx}" type="number" onchange="inputBarang(${id}, $('#qty-${id}-${idx}').val(), this.value, ${max})" name="harga_jual[${id}][]" value="${price}">
            </td>
            <td id="total-${id}-${idx}" style="border: 1px solid #ddd; padding: 12px; text-align: center;" value=""></td>
        </tr>`;

    // Append baris baru ke tabel
    $('#tbody-' + id).append(html);
}




// Fungsi untuk validasi jumlah kuantitas
function validateQuantity(id, idx, max) {
    let inputs = $(`#tbody-${id} input[name^="jumlah[${id}][]"]`); // Ambil semua input jumlah
    let total = 0;

    // Hitung total dari semua input
    inputs.each(function () {
        total += parseInt($(this).val()) || 0; // Pastikan parseInt
    });

    // Jika total melebihi batas maksimum, reset input yang sedang diubah
    if (total > max) {
        alert("Kuantitas melebihi batas");
        $(`#qty-${id}-${idx}`).val(0); // Reset input ke 0
    }
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
