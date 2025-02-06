<x-Layout.layout>
    <style>
        .modal {
            top: 0;
            left: 0;
            width: 40%;
            z-index: 1000;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            position: relative;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            font-family: Arial, sans-serif;
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
            background-color: #e0a50f;
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
          <label class="input border-none flex items-center gap-2 mt-3">
              Nama Satuan :
              <select name="id_satuan" class="select-field">
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

          <label class="input border-none flex items-center gap-2 mt-1">
              Status PPN :
              <select id="status_ppn" name="status_ppn" class="select-field"
                  onchange="updatePPNValue()">
                  <option disabled selected>Status PPN</option>
                  <option value="ya">YA</option>
                  <option value="tidak">TIDAK</option>
              </select>
          </label>
          <label class="input border-none flex items-center gap-2 mt-1">
              Status Barang :
              <select name="status" class="select-field">
                  <option disabled selected>Status Barang</option>
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
              pageLength: 20,
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
                  valuePPNInput.value = 12; // Set 100 jika PPN ya
              } else if (statusPPN === "tidak") {
                  valuePPNInput.value = 12; // Set 0 jika PPN tidak
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
          <h3 class="text-lg font-bold">Edit Data Barang</h3>
          <form action="{{ route('master.barang.edit') }}" method="post">
            @csrf
            <input type="hidden" name="id" value="${id}" class="border-none" />
            <label class="form-label">Kode Objek :</label>
              <input type="text" name="kode_objek" value="${kode_objek}" class="input-field" />
            </label>
            <label class="form-label">Nama :</label>
              <input type="text" name="nama" value="${nama}" class="input-field" />
           
            <input type="hidden" name="nama_singkat" value="${nama}" />
            <label class="form-label">Value : </label>
              <input type="text" name="value" value="${value}" class="input-field" />
            <label class="form-label">Status PPN :</label>
              <select name="status_ppn" class="select-field">
                <option selected>${status_ppn}</option>
                <option value="ya">YA</option>
                <option value="tidak">TIDAK</option>
              </select>
            <label class="form-label">Status Barang : </label>
            <select name="status" class="select-field">
              <option selected>${status}</option>
              <option value="AKTIF">AKTIF</option>
              <option value="NON-AKTIF">NON-AKTIF</option>
            </select>
            <label class="form-label">Nama Satuan :</label>
              <select name="id_satuan" class="select-field">
                <option readonly value="${id_satuan}" selected>${nama_satuan}</option>
                @foreach ($satuan as $satu)
                <option value="{{ $satu->id }}"> {{ $satu->nama_satuan }}</option>
                @endforeach
              </select>
            <label class="form-label">Nilai (%)PPN:</label>
              <input type="number" name="value_ppn" value="${value_ppn}" class="input-field" />
            <button type="submit" class="submit-button">Edit</button>
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
