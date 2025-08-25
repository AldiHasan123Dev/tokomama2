<x-Layout.layout>
    <style>
        .modal {
            top: 0;
            left: 0;
            width: 40%;
            z-index: 1000;
            overflow: visible;
        }

        .stock-card {
            color: rgb(7, 140, 36);
            padding: 15px;
            /* Padding agar terlihat lebih rapi */
            border-radius: 8px;
            /* Membuat sudut card membulat */
            font-weight: bold;
            /* Membuat teks lebih tegas */
            text-align: center;
            /* Pusatkan teks */
            margin-bottom: 15px;
            /* Beri jarak bawah */
        }

        .kembali-button {
            display: inline-block;
            padding: 12px 10px;
            background-color: #ad0f0f;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .kembali-button:hover {
            background-color: #761408;
        }

        .modal-box {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: Arial, sans-serif;
        }


        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            background: none;
            border: none;
            color: #333;
            cursor: pointer;
        }

        /* Form labels and containers */
        .form-label {
            display: block;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .input-field,
        .select-field {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
            font-size: 14px;
        }

        /* Submit button */
        .submit-button {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        /* Label for extra info */
        .label-info {
            font-size: 12px;
            color: red;
        }
    </style>
    <div id="dialog"></div>
    <dialog id="my_modal_3" class="modal">
        <div class="modal-box">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" onclick="closeModal()">✕</button>
            </form>
            <h1 class="text-lg mb-3">Form Tambah Barang Surat Jalan</h1>
            <p class="text-sm text-red-500 mt-1">* List stock barang akan muncul sesuai status ppn yang di pilih.</p>
            <form action="{{ route('surat-jalan.tambahBarang') }}" method="post">
                @csrf
                <input type="hidden" id="modal_data" name="id_surat_jalan" value="" readonly>
                <label for="id_barang" class="form-label">Stock Barang PPN</label>
                <select class="select-field" name="id_barang_ppn" id="id_barang">
                    <option value=""></option>
                    @foreach ($stocksPpn as $bar)
                        <option value="{{ $bar->id }}">{{ $bar->nama }} || {{ $bar->nama_satuan }} ||
                            {{ $bar->value }} - {{ $bar->kode_objek }} || {{ $bar->sisa }}
                            || {{ $bar->no_bm }}
                        </option>
                    @endforeach
                </select>
                <label for="id_barang1" class="form-label">Stock Barang No PPN</label>
                <select class="select-field" name="id_barang_no_ppn" id="id_barang1">
                    <option value=""></option>
                    @foreach ($stocksNoppn as $bar)
                        <option value="{{ $bar->id }}">{{ $bar->nama }} || {{ $bar->nama_satuan }} ||
                            {{ $bar->value }} - {{ $bar->kode_objek }} || {{ $bar->sisa }}
                            || {{ $bar->no_bm }}</option>
                    @endforeach
                </select>
                <label for="jumlah_jual" class="form-label">Jumlah Jual</label>
                <input type="number" name="jumlah_jual" id="jumlah_jual" class="input-field input-sm w-full">
                {{-- <label for="satuan_jual" class="form-label">Satuan Jual</label>
                <select class="select-field" name="satuan_jual" id="satuan_jual">
                    @foreach ($satuans as $satu)
                        <option value="{{ $satu->nama_satuan }}">{{ $satu->nama_satuan }}</option>
                    @endforeach
                </select> --}}
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" class="input-field input-sm w-full">
                <button type="submit" class="submit-button">Tambah Barang</button>
            </form>
        </div>
    </dialog>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Edit By Barang</x-slot:tittle>
        <a href="{{ route('surat-jalan.index') }}" class="kembali-button mt-5">Kembali ke List Surat Jalan</a>
        <div class="overflow-x-auto">
            <table class="table" id="editBarang">
                <!-- head -->
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Nomor Surat Jalan</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Jual</th>
                        <th>Satuan Jual</th>
                        {{-- <th>Margin</th> --}}
                        <th>Keterangan</th>
                        <th>No BM</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trans)
                        <tr>
                            <td>
                                @if ($trans->sisa > 0)
                                <button
                                    onclick="openModal({{ $trans->suratJalan->id }}, '{{ $trans->barang->status_ppn }}')">
                                    <i class="fa-solid fa-plus text-green-500 mr-5"></i>
                                </button>
                                <button
                                    onclick="getData({{ $trans->id }}, {{ $trans->id_supplier }}, {{ $trans->jumlah_jual }}, '{{ $trans->satuan_jual }}', '{{ $trans->suratJalan->nomor_surat }}',' {{ $trans->suppliers->nama }}', '{{ $trans->keterangan }}')"
                                    class="text-yellow-300"><i class="fa-solid fa-pencil"></i></button>
                                <form onsubmit="deleteData({{ $trans->id }}, {{ $trans->id_surat_jalan }}); return false;">
                                        @csrf
                                        @method('delete')
                                        <button type="submit"><i class="fa-solid fa-trash text-red-500"></i></button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $trans->suratJalan->nomor_surat }}</td>
                            <td>{{ $trans->barang->nama }}</td>
                            <td>{{ $trans->jumlah_jual }}</td>
                            <td>
                                {{ $trans->satuan_jual }}
                            </td>
                            {{-- <td @if($trans->satuan_jual !== $trans->satuan_beli) style="background-color: red; color: white;" @endif>
                                {{ $trans->satuan_beli }}
                            </td>                             --}}
                            {{-- <td>{{ number_format($trans->margin) }}</td> --}}
                            <td>{{ $trans->keterangan ? $trans->keterangan : '-' }}</td>
                            <td>{{ $trans->no_bm }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    {{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script> --}}
    <x-slot:script>
        <script>
            // Inisialisasi DataTable
            let table = $('#editBarang').DataTable({
                pageLength: 20,
                ordering: false,
                scrollX: true,
            });

            // Fungsi untuk menampilkan modal edit barang
            function getData(id, id_supplier, kuantitas, satuan, surat_jalan, supplier, keterangan) {
    $.ajax({
        url: `/get-stock/${id}`, // Pastikan rute ini ada di backend
        type: 'GET',
        success: function(response) {
            let sisaStock = response.stock;
            let jumlahJual = response.trx_jumlah_jual; // Pastikan respons dari backend mengandung stock

            $('#dialog').html(`
                <dialog id="my_modal_5" class="modal">
                    <div class="modal-box">
                        <form method="dialog">
                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                        </form>
                        <h3 class="text-lg font-bold">Edit Barang No Surat ${surat_jalan}</h3>
                        <div class="stock-card">
                            <p class="mb-0">Stock: <strong>${sisaStock}</strong></p>
                            <p class="mb-0">Jumlah Jual di SJ: <strong>${jumlahJual}</strong></p>
                        </div>
                        <form action="{{ route('surat-jalan.editBarang') }}" method="post">
                            @csrf
                            <input type="hidden" name="id" value="${id}" />
                            <input type="hidden" name="id_supplier" value="${id_supplier}" />

                            <label class="form-label">Pilih Opsi :</label>
                            <select class="select-field" name="qty" id="qty">
                                <option value="">-- Pilih Opsi --</option>
                                <option value="tambah">Ditambah</option>
                                <option value="kurangi">Dikurangi</option>
                            </select>

                            <!-- Input untuk Tambah -->
                            <div id="tambahInput" style="display: none;">
                                <label class="form-label">Isikan tambahannya (Bukan jadinya):</label>
                                <input type="number" name="tambah" step="any" class="input-field" placeholder="Masukkan nilai yang ditambahkan">
                            </div>

                            <!-- Input untuk Kurangi -->
                            <div id="kurangiInput" style="display: none;">
                                <label class="form-label">Isikan pengurangnya (Bukan jadinya)</label>
                                <input type="number" name="kurangi" class="input-field" step="any" onchange="inputBarang()" placeholder="Masukkan nilai yang dikurangi">
                            </div> 
                            <label class="form-label">Keterangan:</label>
                            <input type="text" name="keterangan" class="input-field" value="${keterangan}" />

                            <button type="submit" class="submit-button">Edit</button>
                        </form>
                    </div>
                </dialog>
            `);

            // Inisialisasi select2
            $('#satuan').select2({
                dropdownParent: $(`#my_modal_5`),
                dropdownAutoWidth: true,
                width: '100%'
            });
            $('#qty').select2({
                dropdownParent: $(`#my_modal_5`),
                dropdownAutoWidth: true,
                width: '100%'
            });

            // Menampilkan modal
            my_modal_5.showModal();

            // Event listener untuk menangani perubahan select qty
            $('#qty').on('change', function () {
                let selectedValue = $(this).val();
                if (selectedValue === "tambah") {
                    document.getElementById('tambahInput').style.display = "block";
                    document.getElementById('kurangiInput').style.display = "none";
                } else if (selectedValue === "kurangi") {
                    document.getElementById('tambahInput').style.display = "none";
                    document.getElementById('kurangiInput').style.display = "block";
                } else {
                    document.getElementById('tambahInput').style.display = "none";
                    document.getElementById('kurangiInput').style.display = "none";
                }
            });

            // Trigger change agar kondisi awal sesuai
            $('#qty').trigger('change');
        }
    });
}



            // Fungsi untuk membuka modal lainnya
            function openModal(data, statusPpn) {
                // Menyimpan data ke input tersembunyi
                document.getElementById('modal_data').value = data;
                document.getElementById('my_modal_3').showModal();

                // Inisialisasi Select2 untuk modal ini
                $('#id_barang').select2({
                    dropdownParent: $('#my_modal_3')
                });
                $('#id_barang1').select2({
                    dropdownParent: $('#my_modal_3')
                });
                $('#id_supplier').select2({
                    dropdownParent: $('#my_modal_3')
                });
                $('#satuan_jual').select2({
                    dropdownParent: $('#my_modal_3')
                });

                // Filter barang berdasarkan status_ppn
                if (statusPpn === 'ya') {
                    $('#id_barang').prop('disabled', false);
                    $('#id_barang1').prop('disabled', true).val(null).trigger('change'); // Reset dan disable yang non-PPN
                } else {
                    $('#id_barang').prop('disabled', true).val(null).trigger('change'); // Reset dan disable yang PPN
                    $('#id_barang1').prop('disabled', false);
                }

                // Pastikan hanya satu select yang bisa dipilih
                // $('#id_barang').on('change', function () {
                //     if ($(this).val()) {
                //         $('#id_barang1').prop('disabled', true).val(null).trigger('change');
                //     } else {
                //         $('#id_barang1').prop('disabled', false);
                //     }
                // });

                // $('#id_barang1').on('change', function () {
                //     if ($(this).val()) {
                //         $('#id_barang').prop('disabled', true).val(null).trigger('change');
                //     } else {
                //         $('#id_barang').prop('disabled', false);
                //     }
                // });
            }


            // Fungsi untuk menutup modal
            function closeModal() {
                document.getElementById('my_modal_3').close();
            }


            // Fungsi untuk menghapus data dengan konfirmasi
            function deleteData(id, id_surat_jalan) {
                // Pertama, lakukan pengecekan jumlah barang di surat jalan
                $.ajax({
                    method: 'GET',
                    url: "{{ route('surat-jalan.checkBarangCount') }}", // Sesuaikan dengan route untuk mengecek jumlah barang
                    data: {
                        id_surat_jalan: id_surat_jalan // Kirim ID surat jalan untuk mengecek jumlah barang
                    },
                    success: function(response) {
                        // Tampilkan nilai count di console
                        console.log('Jumlah barang di surat jalan: ', response.count);

                        // Cek apakah barang yang akan dihapus adalah satu-satunya barang
                        if (response.count === 1) {
                            // Jika hanya ada 1 barang di surat jalan
                            alert(
                                'Ini adalah satu-satunya barang dalam surat jalan yang akan anda hapus, silakan tambahkan barang terlebih dahulu');
                        } else {
                            // Tampilkan konfirmasi untuk penghapusan jika barang lebih dari 1
                            if (confirm('Apa anda yakin akan menghapus data ini?')) {
                                // Jika user menekan 'OK', lanjutkan dengan penghapusan data
                                $.ajax({
                                    method: 'DELETE',
                                    url: "{{ route('surat-jalan.hapusBarang') }}",
                                    data: {
                                        id: id
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(response) {
                                        alert("Data Surat Jalan berhasil dihapus!");
                                        location.reload(); // Refresh halaman setelah data dihapus
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('Error:', error);
                                        console.log('Status:', status);
                                        console.dir(xhr);
                                        console.log('Response:', xhr.responseJSON);
                                    }
                                });
                            } else {
                                // Jika user menekan 'Cancel', tidak ada aksi yang dilakukan
                                console.log("Penghapusan dibatalkan");
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                        console.log('Status:', status);
                        console.dir(xhr);
                        console.log('Response:', xhr.responseJSON);
                    }
                });
            }
        </script>

    </x-slot:script>
</x-Layout.layout>
