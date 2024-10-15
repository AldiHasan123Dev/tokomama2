<x-Layout.layout>
    <div id="dialog"></div>

    <x-keuangan.card-keuangan>
        <x-slot:tittle>List Invoice External</x-slot:tittle>
        <div class="overflow-x-auto">
            <table class="table" id="table-supplier">
                <!-- head -->
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Surat Jalan</th>
                        <th>Supplier</th>
                        <th>Invoice External</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </x-keuangan.card-keuangan>

    <x-slot:script>
        <script>
            let table = $(`#table-supplier`).DataTable({
                pageLength: 100,
                ajax: {
                    method: "POST",
                    url: "{{ route('surat-jalan-supplier.data') }}",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columns: [{
                        data: 'aksi',
                        name: 'aksi'
                    },
                    {
                        data: 'nomor_surat',
                        name: 'nomor_surat'
                    },
                    {
                        data: 'supplier',
                        name: 'supplier'
                    },
                    {
                        data: 'invoice_external',
                        name: 'invoice_external'
                    }
                ]
            });

            function getData(id_surat_jalan, nomor_surat, id_supplier, nama_supplier, invoice_external) {
                $('#dialog').html(`
    <dialog id="my_modal_5" class="modal">
        <div class="modal-box w-11/12 max-w-2xl pl-10">
            <form method="dialog">
                <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
            </form>
            <h3 class="text-lg font-bold">Edit Data</h3>
            <form id="editForm" action="{{ route('surat-jalan-external.data.edit') }}" method="post">
                @csrf
                <input type="hidden" name="id_surat_jalan" value="${id_surat_jalan}" />
                <label class="input border flex items-center gap-2 mt-3">
                    Nomor Surat :
                    <input type="text" name="nomor_surat" value="${nomor_surat}" class="border-none" readonly />
                </label>
                <input type="hidden" name="id_supplier" value="${id_supplier}" />
                <label class="input border flex items-center gap-2 mt-3">
                    Nama Supplier :
                    <input type="text" name="nama_supplier" value="${nama_supplier}" class="border-none" readonly />
                </label>
                <label class="input border flex items-center gap-2 mt-3">
                    Invoice External :
                    <input type="text" id="invoice_external" name="invoice_external" value="${invoice_external}" required class="border-none" autofocus />
                </label>
                <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-2">Edit</button>
            </form>
        </div>
    </dialog>`);

                // Validasi ketika form di-submit
                $('#editForm').on('submit', function(e) {
                    let invoiceExternal = $('#invoice_external').val().trim();
                    if (invoiceExternal === "") {
                        e.preventDefault(); // Mencegah pengiriman form
                        alert("Invoice external diisi terlebih dahulu"); // Menampilkan alert
                    }
                });

                document.getElementById('my_modal_5').showModal();
            }
        </script>
    </x-slot:script>
</x-Layout.layout>
