<x-Layout.layout>

  <div id="dialog"></div>

  <x-master.card-master>
      <x-slot:tittle>Data Barang</x-slot:tittle>
      <div class="overflow-x-auto">
          <table id="table-barang" class="display compact" style="width:100%">
              <thead>
                  <tr>
                      <th>#</th>
                      <th>Kode Objek</th>
                      <th>Nama</th>
                      <th>Value</th>
                      <th>Satuan Standart</th>
                      <th>Status PPN</th>
                      <th>Status Barang</th>
                      <th>(%)PPN</th>
                      <th>Aksi</th>
                  </tr>
              </thead>
              <tbody>
              </tbody>
          </table>
      </div>
  </x-master.card-master>

  <x-master.card-master>
      <x-slot:tittle>Menambah Data Barang</x-slot:tittle>
      <form action="{{ route('master.barang.add') }}" method="post" class="grid grid-cols-3 gap-5">
          @csrf
          <label class="form-control w-full max-w-xs col-start-1">
              <div class="label">
                  <span class="label-text">Kode Objek <span class="text-red-500">*</span></span>
              </div>
              <input type="text" placeholder="Kode Barang" name="kode_objek"
                  class="input input-bordered w-full max-w-xs rounded-md" required />
          </label>
          <label class="form-control w-full max-w-xs col-start-2">
              <div class="label">
                  <span class="label-text">Nama <span class="text-red-500">*</span></span>
              </div>
              <input id="nama" type="text" placeholder="Nama Barang" name="nama"
                  class="input input-bordered w-full max-w-xs rounded-md" required />
          </label>
          <input type="hidden" id="nama_singkat" name="nama_singkat" />
          <label class="input border flex items-center gap-2 mt-3">
              Nama Satuan :
              <select name="id_satuan" class="select select-sm select-bordered w-full max-w-xs">
                  <option disabled selected>Satuan</option>
                  @foreach ($satuan as $satu)
                      <option value="{{ $satu->id }}"> {{ $satu->nama_satuan }}</option>
                  @endforeach
              </select>
          </label>
          <label class="form-control w-full max-w-xs col-start-1">
              <div class="label">
                  <span class="label-text">Value <span class="text-red-500">*</span></span>
              </div>
              <input type="text" placeholder="10" name="value"
                  class="input input-bordered w-full max-w-xs rounded-md" required />
          </label>
          <label class="form-control w-full max-w-xs col-start-2">
              <div class="label">
                  <span class="label-text">Value PPN<span class="text-red-500">*</span></span>
              </div>
              <input type="number" id="value_ppn" name="value_ppn" placeholder="Pilih status ppn terlebih dahulu"
                  class="input input-bordered w-full max-w-xs rounded-md" readonly disabled required />
          </label>

          <label class="input border flex items-center gap-2 mt-1">
              Status PPN :
              <select id="status_ppn" name="status_ppn" class="select select-sm select-bordered w-full max-w-xs"
                  onchange="updatePPNValue()">
                  <option disabled selected>status</option>
                  <option value="ya">YA</option>
                  <option value="tidak">TIDAK</option>
              </select>
          </label>
          <label class="input border flex items-center gap-2 mt-1">
              Status Barang :
              <select name="status" class="select select-sm select-bordered w-full max-w-xs">
                  <option disabled selected>status</option>
                  <option value="AKTIF">AKTIF</option>
                  <option value="NON-AKTIF">NON-AKTIF</option>
              </select>
          </label>
          <div class="col-span-3 mt-8 text-center">
              <button type="submit" class="btn text-semibold text-white bg-green-500 w-1/3 mx-auto">Simpan Data
                  Barang</button>
          </div>
      </form>


  </x-master.card-master>

  <x-slot:script>
      <script>
          let table = new DataTable('#table-barang', {
              pageLength: 100,
              ajax: {
                  url: "{{ route('master.barang.list') }}",
                  data: {
                      _token: "{{ csrf_token() }}"
                  }
              },
              columns: [{
                      data: 'DT_RowIndex',
                      name: 'number'
                  },
                  {
                      data: 'kode_objek',
                      name: 'kode objek'
                  },
                  {
                      data: 'nama',
                      name: 'nama'
                  },
                  {
                      data: 'value',
                      name: 'value'
                  },
                  {
                      data: 'nama_satuan',
                      name: 'nama_satuan'
                  },
                  {
                      data: 'status_ppn',
                      name: 'status_ppn'
                  },
                  {
                      data: 'status',
                      name: 'status'
                  },
                  {
                      data: 'value_ppn',
                      name: 'value_ppn'
                  },
                  {
                      data: 'aksi',
                      name: 'aksi'
                  },
                  {
                      data: 'id',
                      name: 'id',
                      visible: false
                  },
              ]
          });

          function updatePPNValue() {
              // Ambil elemen input dan select
              var statusPPN = document.getElementById("status_ppn").value;
              var valuePPNInput = document.getElementById("value_ppn");

              // Set value_ppn berdasarkan pilihan status_ppn
              if (statusPPN === "ya") {
                  valuePPNInput.value = 11; // Set 100 jika PPN ya
              } else if (statusPPN === "tidak") {
                  valuePPNInput.value = 0; // Set 0 jika PPN tidak
              }
          }

          $('#nama').on('keyup', function() {
              $('#nama_singkat').val(this.value);
          });

          function getData(id, kode_objek, nama, nama_singkat, value, status_ppn, status, value_ppn, nama_satuan, id_satuan) {
              $('#dialog').html(`<dialog id="my_modal_6" class="modal">
          <div class="modal-box  w-11/12 max-w-2xl pl-10 py-9 ">
          <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
          </form>
          <h3 class="text-lg font-bold">Edit Data</h3>
          <form action="{{ route('master.barang.edit') }}" method="post">
            @csrf
            <input type="hidden" name="id" value="${id}" class="border-none" />
            <label class="input border flex items-center gap-2 mt-3">
              Kode Objek :
              <input type="text" name="kode_objek" value="${kode_objek}" class="border-none" />
            </label>
            <label class="input border flex items-center gap-2 mt-4">
              Nama :
              <input type="text" name="nama" value="${nama}" class="border-none" />
            </label>
            <input type="hidden" name="nama_singkat" value="${nama}" />
            <label class="input border flex items-center gap-2 mt-4">
              Value :
              <input type="text" name="value" value="${value}" class="border-none text-slate-400" />
            </label>
            <label class="input border flex items-center gap-2 mt-4">
              Status PPN :
              <select name="status_ppn" class="select select-sm select-bordered w-full max-w-xs">
                <option selected>${status_ppn}</option>
                <option value="ya">YA</option>
                <option value="tidak">TIDAK</option>
              </select>
            </label>
            <label class="input border flex items-center gap-2 mt-1">
            Status Barang :
            <select name="status" class="select select-sm select-bordered w-full max-w-xs">
              <option selected>${status}</option>
              <option value="AKTIF">AKTIF</option>
              <option value="NON-AKTIF">NON-AKTIF</option>
            </select>
          </label>
            <label class="input border flex items-center gap-2 mt-4">
              Nama Satuan :
              <select name="id_satuan" class="select select-sm select-bordered w-full max-w-xs">
                <option readonly value="${id_satuan}" selected>${nama_satuan}</option>
                @foreach ($satuan as $satu)
                <option value="{{ $satu->id }}"> {{ $satu->nama_satuan }}</option>
                @endforeach
              </select>
            </label>
            <label class="input border flex items-center gap-2 mt-4">
              Nilai (%)PPN:
              <input type="number" name="value_ppn" value="${value_ppn}" class="border-none text-slate-400" />
            </label>
            <button type="submit" class="btn bg-green-400 text-white font-semibold w-72 mt-4">Edit</button>
          </form>
          </div>
        </dialog>`);
              my_modal_6.showModal();
          }

          function deleteData(id) {
              if (confirm('Apakah anda ingin menghapus data ini?')) {
                  $.ajax({
                      method: 'post',
                      url: "{{ route('master.barang.delete') }}",
                      data: {
                          id: id
                      },
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      success: function(response) {
                          alert("Data Master Barang berhasil dihapus!");
                          table.ajax.reload();
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
