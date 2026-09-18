<x-Layout.layout>
    <x-keuangan.card-keuangan>
        <style>
            .server-time {
                font-size: 18px;
                font-weight: bold;
                color: #fff04d;
                background-color: #ff0000;
                padding: 10px 15px;
                border-radius: 5px;
                display: inline-block;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            }
        </style>
        <x-slot:tittle>Input Harga Jual untuk Draft Invoice</x-slot:tittle>
        <form action="{{ route('pending.invoice.preview_DraftInv') }}" method="post" id="form">
            @csrf
            <p class="server-time">
                Silakan pilih Tgl Draft Invoice, sebab Tgl dan Jam Server adalah : <span id="server-time">{{ now()->format('Y-m-d H:i:s') }}</span>
            </p>
            <input type="date" name="tgl_draft_invoice" value="{{ date('Y-m-d') }}">
          
            <input type="hidden" name="draft_invoice_count" value="{{ $invoice_count }}">
            
            <div style="overflow-x: auto; margin-top: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
                <table style="border-collapse: collapse; width: 100%; margin: 0 auto;">
                    <!-- head -->
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">#</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Nama Barang</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Satuan</th>
                            <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Jumlah Barang</th>
                            {{-- <th style="border: 1px solid #ddd; padding: 12px; text-align: center;">Harga Beli Satuan</th> --}}
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
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">{{ $item->satuan_jual }}</td>
                            <td hidden style="border: 1px solid #ddd; padding: 12px; text-align: center;" class="invoice-{{ $item->id }}">
                                <select hidden name="draft_invoice[{{ $item->id }}][]" class="select w-full">
                                    <option value="1" selected>Invoice Ke - 1</option>
                                    @for ($i = 2; $i <= $loop->index + 1; $i++)
                                        <option value="{{ $i }}" {{ $i == $loop->index + 1 ? 'selected' : '' }}>Invoice Ke - {{ $i }}</option>
                                    @endfor
                                </select>
                            </td>                       
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: center;">
                                <input type="hidden" id="qty-{{ $item->id }}-1" name="jumlah[{{ $item->id }}][]" value="{{ $item->sisa }}">
                                 <input type="hidden" id="surat_jalan-{{ $item->id }}-1" name="surat_jalan[{{ $item->id }}][]" value="{{ $item->suratJalan->nomor_surat }}">
                                {{ $item->sisa }}
                            </td>
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: end;" id="harga_beli_ppn-{{ $item->id }}-1">
                                @if ($item->barang->status_ppn == 'ya')    
                                    {{ number_format(($item->harga_beli) + (($item->harga_beli * 11 / 12) * ($item->barang->value_ppn / 100))) }}
                                @else
                                    {{ number_format($item->harga_beli) }}
                                @endif
                            </td>                            
                            <td style="border: 1px solid #ddd; padding: 12px; text-align: end;">
                                @if ($item->barang->status_ppn == 'ya')  
                                    <input onclick="this.select()" 
                                           id="price-{{ $item->id }}-1" 
                                           type="text" 
                                           oninput="formatRibuan(this)" 
                                           onchange="inputBarang({{ $item->id }}, $('#qty-{{ $item->id }}-1').val(), getCleanNumber(this.value), {{ $item->jumlah_jual }})" 
                                           name="harga_jual[{{ $item->id }}][]" 
                                           value="{{ number_format(round($item->harga_jual * 1.11), 0, '.', ',') }}">
                                @else
                                    <input onclick="this.select()" 
                                           id="price-{{ $item->id }}-1" 
                                           type="text" 
                                           oninput="formatRibuan(this)" 
                                           onchange="inputBarang({{ $item->id }}, $('#qty-{{ $item->id }}-1').val(), getCleanNumber(this.value), {{ $item->jumlah_jual }})" 
                                           name="harga_jual[{{ $item->id }}][]" 
                                           value="{{ number_format($item->harga_jual, 0, '.', ',') }}">
                                @endif
                            </td>                          
                            <td style="border: 1px solid #ddd; padding: 12px; text-align:end" id="total-{{ $item->id }}-1">
                                @if ($item->barang->status_ppn == 'ya')  
                                {{ number_format(($item->harga_jual * $item->jumlah_jual) * 1.11) }}
                                @else
                                {{ number_format($item->harga_jual * $item->jumlah_jual) }}
                                @endif
                            </td>
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

            <button  class="btn bg-green-500 font-semibold justify-align-center text-white w-full mt-3" type="submit">Preview Draft Invoice</button>
        </form>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
    function formatRibuan(input) {
        let angka = input.value.replace(/,/g, ''); // Hapus semua koma
        input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ','); // Tambahkan format ribuan
    }

    function getCleanNumber(value) {
        let cleanValue = value.replace(/,/g, ''); // Hapus semua koma
        return isNaN(cleanValue) ? 0 : Number(cleanValue); // Konversi ke angka
    }
</script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
             // Fungsi untuk memperbarui waktu
             function updateServerTime() {
                 fetch("{{ route('server.time') }}")
                     .then(response => response.json()) // Mengambil data JSON dari server
                     .then(data => {
                         document.getElementById("server-time").textContent = data.time; // Update elemen dengan id "server-time"
                     })
                     .catch(error => console.error('Error fetching server time:', error)); // Tangani error
             }
     
             // Perbarui waktu server pertama kali saat halaman dimuat
             updateServerTime();
     
             // Perbarui setiap detik
             setInterval(updateServerTime, 1000);
         });
         </script>
    <script>
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
