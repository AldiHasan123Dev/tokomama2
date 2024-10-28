<x-Layout.layout>
    <style>
        .modal {
            top: 0;
            left: 0;
            width: 40%;
            z-index: 1000;
        }
        .kembali-button {
    display: inline-block; 
    padding: 12px 10px; 
    background-color: #ad0f0f; 
    color: white; 
    text-decoration: none; 
    border-radius: 8px;
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
            <form action="{{ route('surat-jalan.tambahBarang') }}" method="post">
                @csrf
                <input type="hidden" id="modal_data" name="id_surat_jalan" value="" readonly>
                <label for="id_barang" class="form-label">Barang</label>
                <select class="select-field" name="id_barang" id="id_barang">
                    @foreach ($barangs as $bar)
                        <option value="{{ $bar->id }}">{{ $bar->nama }} || {{ $bar->satuan->nama_satuan }} || {{ $bar->value }}  - {{ $bar->kode_objek }}</option>
                    @endforeach
                </select>
                <label for="id_supplier" class="form-label">Supplier</label>
                <select class="select-field" name="id_supplier" id="id_supplier">
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->nama }}</option>
                    @endforeach
                </select>
                <label for="jumlah_jual" class="form-label">Jumlah Jual & Jumlah Beli</label>
                <input type="number" name="jumlah_jual" id="jumlah_jual" class="input-field input-sm w-full">
                <label for="satuan_jual" class="form-label">Satuan Jual & Satuan Beli</label>
                <select class="select-field" name="satuan_jual" id="satuan_jual">
                    @foreach ($satuans as $satu)
                        <option value="{{ $satu->nama_satuan }}">{{ $satu->nama_satuan }}</option>
                    @endforeach
                </select>
                <label for="keterangan" class="form-label">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" class="input-field input-sm w-full">
                <button type="submit" class="submit-button">Tambah Barang</button>
            </form>
        </div>
    </dialog>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Edit By Barang</x-slot:tittle>
        <a href="{{ route('surat-jalan.index') }}" class="kembali-button">Kembali ke List Surat Jalan</a>
        <div class="overflow-x-auto">
            <table class="table" id="editBarang">
                <!-- head -->
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Nomor Surat Jalan</th>
                        <th>Nama Barang</th>
                        <th>Jumlah Jual</th>
                        <th>Jumlah Beli</th>
                        <th>Harga Jual</th>
                        <th>Harga Beli</th>
                        <th>Satuan Jual</th>
                        <th>Satuan Beli</th>
                        <th>Margin</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trans)
                        <tr>
                            <td>
                                @if ($trans->sisa > 0)
                                    <button onclick="openModal({{ $trans->suratJalan->id }})"><i class="fa-solid fa-plus text-green-500 mr-5"></i></button>
                                    <button onclick="getData({{ $trans->id }}, {{ $trans->jumlah_jual }}, '{{ $trans->satuan_jual }}', '{{  $trans->suratJalan->nomor_surat }}')" class="text-yellow-300"><i class="fa-solid fa-pencil"></i></button>
                                    <form action="{{ route('surat-jalan.hapusBarang') }}" method="post">
                                        @csrf
                                        @method('delete')
                                        <input type="hidden" name="id" value="{{ $trans->id }}">
                                        <button type="submit"><i class="fa-solid fa-trash text-red-500"></i></button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ $trans->suratJalan->nomor_surat }}</td>
                            <td>{{ $trans->barang->nama }}</td>
                            <td>{{ $trans->jumlah_jual }}</td>
                            <td>{{ $trans->jumlah_beli }}</td>
                            <td>{{ $trans->harga_jual }}</td>
                            <td>{{ $trans->harga_beli }}</td>
                            <td>{{ $trans->satuan_jual }}</td>
                            <td>{{ $trans->satuan_beli }}</td>
                            <td>{{ number_format($trans->margin) }}</td>
                            <td>{{ $trans->keterangan ? $trans->keterangan : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    {{-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script> --}}
    <x-slot:script>
        <script>
            let table = new DataTable('#editBarang', {
                pageLength: 100,
                order: [[1, 'desc']]
            });

            function getData(id, kuantitas, satuan, surat_jalan) {
    $('#dialog').html(`
        <dialog id="my_modal_5" class="modal">
            <div class="modal-box">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                <h3 class="text-lg font-bold">Edit Barang No Surat ${surat_jalan}</h3>
                <form action="{{ route('surat-jalan.editBarang') }}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="${id}" class="border-none" />
                    <label class="form-label">Jumlah Jual & Jumlah Beli :</label>
                    <input type="text" name="jumlah_jual" class="input-field" value="${kuantitas}" />
                    <label class="form-label">Satuan</label>
                    <select class="select-field" name="satuan" id="satuan">
                        @foreach ($satuans as $s)
                            <option value="{{ $s->nama_satuan }}" ${satuan === '{{ $s->nama_satuan }}' ? 'selected' : ''}>{{ $s->nama_satuan }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="submit-button">Edit</button>
                </form>
            </div>
        </dialog>
    `);

                my_modal_5.showModal();
            }

            function openModal(data) {
                // Set the data to the hidden input field
                document.getElementById('modal_data').value = data;
                // Show the modal
                document.getElementById('my_modal_3').showModal();

                $('#id_barang').select2({
                    dropdownParent: $(`#my_modal_3`),
                });

                $('#id_supplier').select2({
                    dropdownParent: $(`#my_modal_3`),
                });

                $('#satuan_jual').select2({
                    dropdownParent: $(`#my_modal_3`),
                });
            }

            function closeModal() {
                // Close the modal
                document.getElementById('my_modal_3').close();
            }

            function deleteData(id) {
                if (confirm('Apa kamu yakin akan menghapus data ini?')) {
                    $.ajax({
                        method: 'POST',
                        url: "{{ route('surat-jalan.hapusBarang') }}",
                        data: { 
                            id: id
                        },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            alert("Data Surat Jalan berhasil dihapus!");
                            table.ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                            console.log('Status:', status);
                            console.dir(xhr);
                            console.log('Response:', xhr.responseJSON);
                        }
                    });
                }
            }
        </script>
    </x-slot:script>
</x-Layout.layout>
