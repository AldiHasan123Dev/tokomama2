<x-Layout.layout>

  <div id="satu"></div>
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
  <x-master.card-master>
    <x-slot:tittle>Table Customer</x-slot:tittle>
    <div class="overflow-x-auto">
      <table class="table" id="table-customer">
        <thead>
          <tr>
            <th>ID</th>
            <th>Sales</th>
            <th>Nama</th>
            <th>NPWP</th>
            <th>TOP
            </th>
            <th>Email</th>
            <th>No.Telp</th>
            <th>Alamat</th>
            <th>Kota</th>
            <th>Nama NPWP</th>
            <th>Alamat NPWP</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </x-master.card-master>



  <x-slot:script>
    <script>
      let table = $('#table-customer').DataTable({
            pageLength: 100,
            ajax: {
              url: "{{route('master.customer.list')}}",
              
              data:{
                _token: "{{csrf_token()}}"
              }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'number'},
                { data: 'sales', name: 'sales' },
                { data: 'nama', name: 'nama' },
                { data: 'npwp', name: 'npwp' },
                { data: 'top', name: 'top'},
                { data: 'email', name: 'email' },
                { data: 'no_telp', name: 'no_telp' },
                { data: 'alamat', name: 'alamat' },
                { data: 'kota', name: 'kota' },
                { data: 'nama_npwp', name: 'nama_npwp' },
                { data: 'alamat_npwp', name: 'alamat_npwp' },
                { data: 'status', name: 'status' },
                { data: 'id', name: 'id', visible:false},
            ]
          })

         

         


$(document).on('change', '.status-switch', function (e) {
    const checkbox = $(this);
    const customerId = checkbox.data('id');
    const customerName = checkbox.data('name');
    const newStatus = checkbox.is(':checked') ? 0 : 1; // checked = Aktif (0), unchecked = Blokir (1)
    const statusText = newStatus == 1 ? 'diblokir' : 'diaktifkan';

    if (!confirm(`Yakin ingin ${customerName} akan ${statusText}?`)) {
        // Batalkan perubahan toggle
        checkbox.prop('checked', !checkbox.is(':checked'));
        return;
    }

    $.ajax({
        url: "{{ route('master.customer.blokir.update') }}",
        method: 'POST',
        data: {
            id: customerId,
            is_block: newStatus,
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            alert(`${customerName} berhasil ${statusText}!`);
            table.ajax.reload(null, false); // ⬅️ ini untuk refresh tanpa reset halaman
        },
        error: function () {
            alert('Gagal mengubah status.');
            checkbox.prop('checked', !checkbox.is(':checked'));
        }
    });
});








    </script>
  </x-slot:script>
</x-Layout.layout>