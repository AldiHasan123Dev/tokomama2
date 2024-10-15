<x-Layout.layout>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .modal {
            z-index: 1000; /* Set a lower z-index than dropdown */
        }
    </style>
    <input type="hidden" id="nj" value="{{ $data[0]->nomor }}">
    <input type="hidden" id="tgl" value="{{ $tgl }}">
    
    <div id="dialog"></div>

    <x-jurnal.card-jurnal>
        <x-slot:tittle>Parameter</x-slot:tittle>
        <div class="grid grid-cols-3 gap-4  ">
            <p class="bg-gray-500 text-white p-1 text-center">[1] Customer</p>
            <p class="bg-gray-500 text-white p-1 text-center">[2] Supplier</p>
            <p class="bg-gray-500 text-white p-1 text-center">[3] Barang</p>
            <p class="bg-gray-500 text-white p-1 text-center">[4] Kuantitas</p>
            <p class="bg-gray-500 text-white p-1 text-center">[5] Satuan</p>
            <p class="bg-gray-500 text-white p-1 text-center">[6] Harsat Beli</p>
            <p class="bg-gray-500 text-white p-1 text-center">[7] Harsat Jual</p>
            <p class="bg-gray-500 text-white p-1 text-center">[8] Keterangan</p>
        </div>
    </x-jurnal.card-jurnal>
    <x-jurnal.card-jurnal>
        <x-slot:tittle>Edit Jurnal</x-slot:tittle>
        <div class="overflow-x-auto">
            <form action="{{ route('jurnal.edit.tglupdate') }}" method="post">
                @csrf
                <input name="tgl_input" type="date" class="mb-8 rounded-md" id="tgl_input">
                <input readonly name="nomor_jurnal_input" type="text" class="mb-8 rounded-md bg-gray-100" id="nomor_jurnal">
                <button type="submit" class="btn bg-green-500 font-semibold text-white">Simpan Tanggal</button>
            </form>

            <table id="table-editj"  class="cell-border hover display nowrap" >
              <thead>
                <tr>
                  <th>Aksi</th>
                  <th>ID</th>
                  <th>Nomor Jurnal</th>
                  <th>Akun</th>
                  <th>Nama Akun</th>
                  <th>Debit</th>
                  <th>Kredit</th>
                  <th>Keterangan</th>
                  <th>Invoice</th>
                  <th>Invoice External</th>
                  <th>Nopol</th>
                  <th>Tipe</th>
                  <th>Keterangan Buku Besar Pembantu</th>
                  
                </tr>
              </thead>
              <tbody>
                @foreach ($data as $item)
                  <tr>
                    <td><button class="text-yellow-400" onclick="editJurnal( '{{$item->id}}', '{{$item->nomor}}', '{{$item->tgl}}', '{{$item->debit}}', '{{$item->kredit}}', '{{$item->keterangan}}', '{{$item->invoice}}', '{{$item->invoice_external}}', '{{$item->nopol}}', '{{$item->tipe}}', '{{$item->coa_id}}', '{{$item->nama_akun}}', '{{$item->no_akun}}', '{{$item->keterangan_buku_besar_pembantu}}')"><i class="fa-solid fa-pencil"></i></button> |
                    <button id="delete-faktur-all" onclick="deleteItemJurnal('{{$item->id}}')" class="text-red-600 font-semibold mb-3 self-end"><i class="fa-solid fa-trash"></i></button></td>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->nomor ?? '-' }}</td>
                    <td>{{ $item->no_akun ?? '-' }}</td>
                    <td>{{ $item->nama_akun ?? '-' }}</td>
                    <td class="text-end">{{ number_format($item->debit, 2, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->kredit, 2, ',', '.') }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                    <td>{{ $item->invoice == 0 ? '' : ($item->invoice ?? '-') }}</td>
                    <td>{{ $item->invoice_external == 0 ? '' : ($item->invoice_external ?? '-') }}</td>
                    <td>{{ $item->nopol ?? '-' }}</td>
                    <td>{{ $item->tipe ?? '-' }}</td>
                    <td>{{ $item->keterangan_buku_besar_pembantu ?? '-'}}</td>
                    
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          @php
            $total_kredit = $data->sum('kredit');
            $total_debet = $data->sum('debit');
          @endphp
          <p class="font-bold text-2xl">Total Debet: {{number_format($total_debet, 2, ',', '.') }} </p>
          <p class="font-bold text-2xl">Total Kredit:  {{number_format($total_kredit, 2, ',', '.') }}</span> </p>
    </x-jurnal.card-jurnal>

    <x-slot:script>
        <script src="https://cdn.datatables.net/2.1.0/js/dataTables.tailwindcss.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        
        <script>
            let nomorJurnal = $("#nj").val();
            let tgl = $("#tgl").val();

            $("#tgl_input").val(tgl);
            $("#nomor_jurnal").val(nomorJurnal);

            let table = $(`#table-editj`).DataTable({});

            function editJurnal(id, nomor, tgl, debit, kredit, keterangan, invoice, invoice_external, nopol, tipe, coa_id, nama_akun, no_akun, keterangan_buku_besar_pembantu) {
                invoice = (invoice === '0') ? '' : invoice;
                invoice_external = (invoice_external === '0') ? '' : invoice_external;
                $("#dialog").html(`
                    <dialog id="my_modal_3" class="modal">
                        <div class="modal-box w-11/12 max-w-2xl pl-10 py-9">
                            <form method="dialog">
                            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                            </form>
                            <h3 class="text-lg font-bold mb-3">Edit Jurnal</h3>
                            <form action="{{route('jurnal.edit.update')}}" method="post">
                                @csrf
                                <label class="input input-bordered flex items-center gap-2 mt-3">
                                    ID
                                    <input name="id" type="text" class="border-none rounded-sm w-full" class="grow" readonly value="${id}" />
                                </label>
                                    <input name="tipe" type="hidden" value="${tipe}" />
                                <label class="input input-bordered flex items-center gap-2 mt-3">
                                    Nomor Jurnal
                                    <input name="nomor" type="text" class="border-none rounded-sm w-full" class="grow" readonly value="${nomor}" />
                                </label>
                         
                                <input name="tgl" type="hidden" class="border-none rounded-sm w-full" class="grow" value="${tgl}" />
                               
                                <label class="form-control w-full mb-3">
                                    <div class="label">
                                        <span class="label-text">Nopol</span>
                                    </div>
                                    <select class="select select-bordered w-full"  name="nopol" id="nopol">
                                        <option value="${nopol}" selected>${nopol}</option>
                                        @foreach ($nopol as $n)
                                        <option class="z-10" value="{{ $n->nopol}}">{{$n->nopol}}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="form-control w-full mb-3">
                                    <div class="label">
                                        <span class="label-text">Invoice</span>
                                    </div>
                                    <select class="select select-bordered w-full"  name="invoice" id="invoices">
                                        <option value="${invoice}" selected>${invoice}</option>
                                     @foreach ($invProc as $i)
                                    <option class="z-10" value="{{ $i }}">{{$i }}</option>
                                    @endforeach
                                    </select>
                                </label>
                                <label class="form-control w-full mb-3">
                                    <div class="label">
                                        <span class="label-text">Coa</span>
                                    </div>
                                    <select class="select select-bordered w-full"  name="coa_id" id="coa_id">
                                        <option value="${coa_id}" selected>${no_akun} ${nama_akun}</option>
                                        @foreach ($coa as $c)
                                        <option class="z-10" value="{{ $c->id }}">{{$c->no_akun}} {{ $c->nama_akun }}</option>
                                        @endforeach
                                    </select>
                                </label>
                                <label class="input input-bordered flex items-center gap-2 mt-3">
                                    Debit
                                    <input name="debit" type="text" class="border-none rounded-sm w-full" class="grow" value="${debit}" />
                                </label>
                                <label class="input input-bordered flex items-center gap-2 mt-3">
                                    Kredit
                                    <input name="kredit" type="text" class="border-none rounded-sm w-full" class="grow" value="${kredit}" />
                                </label>
                                <label class="form-control w-full mt-3">
                                     <div class="label">
                                        <span class="label-text">Keterangan</span>
                                    </div>
                                    <input name="keterangan" type="text" class="input-border rounded-md w-full" class="grow" value="${keterangan}" />
                                    <div class="label">
                                        <span class="label-text-alt"><span class="text-red-500">*ex:</span> [1]customer [2]supplier [3]barang</span>
                                    </div>
                                </label>
                                <label class="form-control w-full mb-3">
                                    <div class="label">
                                        <span class="label-text">Invoice External</span>
                                    </div>
                                    <select class="select select-bordered w-full"  name="invoice_external" id="invext">
                                        <option value="${invoice_external}" selected>${invoice_external}</option>
                                        @foreach ($invExtProc as $in)
                                        <option class="z-10" value="{{ $in }}">{{$in}}</option>
                                        @endforeach
                                    </select>
                                </label>
                                 <label class="form-control w-full mt-3">
                                     <div class="label">
                                        <span class="label-text">Keterangan Buku Pembantu</span>
                                    </div>
                                    <input name="keterangan_buku_besar_pembantu" type="text" class="input-border rounded-md w-full" class="grow" value="${keterangan_buku_besar_pembantu}" />
                                    <div class="label">
                                    </div>
                                </label>
                                <button type="submit" class="btn w-full bg-green-500 text-white mt-3">Edit</button>
                            </form>
                        </div>
                    </dialog>
                `);
                my_modal_3.showModal();
                $('#coa_id').select2({
                    dropdownParent: $(`#my_modal_3`),
                });
                $('#nopol').select2({
                    dropdownParent: $(`#my_modal_3`),
                });
                $('#invoices').select2({
                    dropdownParent: $(`#my_modal_3`),
                });
                $('#invext').select2({
                    dropdownParent: $(`#my_modal_3`),
                });
            }

            function deleteItemJurnal(id) {
                if (confirm('Apakah anda ingin menghapus data ini?')) {
                    $.ajax({
                        method: 'post',
                        url: "{{ route('jurnal.item.delete') }}",
                        data: {id: id},
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(response) {
                            alert("Data Master Barang berhasil dihapus!");
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error:', error);
                            console.log('Status:', status);
                            console.dir(xhr);
                        }
                    });
                }
            }

            
        </script>
    </x-slot:script>
</x-Layout.layout>