<x-Layout.layout>

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
                <label for="id_barang" class="label">Barang</label>
                <select class="js-example-basic-single w-full" name="id_barang" id="id_barang">
                    @foreach ($barangs as $bar)
                        <option value="{{ $bar->id }}">{{ $bar->nama }} || {{ $bar->satuan->nama_satuan }} || {{ $bar->value }}  - {{ $bar->kode_objek }}</option>
                    @endforeach
                </select>
                <label for="id_supplier" class="label">Supplier</label>
                <select class="js-example-basic-single w-full" name="id_supplier" id="id_supplier">
                    @foreach ($suppliers as $sup)
                        <option value="{{ $sup->id }}">{{ $sup->nama }}</option>
                    @endforeach
                </select>
                <label for="jumlah_jual" class="label">Jumlah Jual & Jumlah Beli</label>
                <input type="number" name="jumlah_jual" id="jumlah_jual" class="input input-sm w-full">
                <label for="satuan_jual" class="label">Satuan Jual & Satuan Beli</label>
                <select class="js-example-basic-single w-full" name="satuan_jual" id="satuan_jual">
                    @foreach ($satuans as $satu)
                        <option value="{{ $satu->nama_satuan }}">{{ $satu->nama_satuan }}</option>
                    @endforeach
                </select>
                <label for="keterangan" class="label">Keterangan</label>
                <input type="text" name="keterangan" id="keterangan" class="input input-sm w-full">
                <button type="submit" class="btn btn-sm bg-green-400 text-white font-semibold w-full">Tambah Barang</button>
            </form>
        </div>
    </dialog>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>Edit By Barang</x-slot:tittle>
        <a href="{{ route('surat-jalan.index') }}" class="my-3 px-3 py-3 bg-blue-500 text-white w-fit rounded-lg">Kembali ke List Surat Jalan</a>
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
                                    <button onclick="getData({{ $trans->id }}, {{ $trans->jumlah_jual }})" class="text-yellow-300"><i class="fa-solid fa-pencil"></i></button>
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

            function getData(id, kuantitas) {
                $('#dialog').html(`<dialog id="my_modal_5" class="modal">
                <div class="modal-box w-11/12 max-w-2xl pl-10">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                </form>
                    <h3 class="text-lg font-bold">Edit by Barang</h3>
                    <form action="{{route('surat-jalan.editBarang')}}" method="post">
                    @csrf
                    <input type="hidden" name="id" value="${id}" class="border-none" />
                    <label class="input border flex items-center gap-2 mt-3">
                        Jumlah Jual & Jumlah Beli :
                        <input type="text" name="jumlah_jual" value="${kuantitas}" class="border-none" />
                    </label>
                    <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-2">Edit</button>
                    </form>
                </div>
                </dialog>`);
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
                if (confirm('Are you sure you want to delete this data?')) {
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
