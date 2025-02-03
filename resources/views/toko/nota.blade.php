<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nota Barang Masuk</title>
    <style>
        @page {
            size: 21.59cm 13.97cm;
            margin: 145px 0px 50px 0px;
        }

        body {
            font-family: Arial, sans-serif;
            width: 100%;
            font-size: 0.9rem;
            margin: 0;
            padding: 0;
        }

        .header {
            position: fixed;
            top: -110px;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px;
            font-size: 1.5rem;
        }

        .content {
            margin: 20px;
            font-size: 1rem;
        }

        p {
            margin: 5px 0;
        }

        table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            border: 1px solid #000;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 1rem;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
            font-size: 0.9rem;
        }

        .footer-content {
            position: fixed;
            bottom: -30px;
            padding-top: 14px;
            width: 100%;
            text-align: center;
        }

        .signature-table {
    width: 90%;
    margin: 0 auto;
    text-align: center;
    font-size: 1rem;
    margin-top: 20px;
    border: none;
}

.signature-table th, .signature-table td {
    border: none;
    text-align: center;
    height: 60px;
}


    </style>
</head>

<body>
    <div class="header">
        <h2>Nota Barang Masuk</h2>
    </div>

    <div class="content">
        <p><strong>Tanggal:</strong> {{ $transaksi->created_at->format('d-m-Y') }}</p>
        <p><strong>Supplier:</strong> {{ $supplier->nama }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $transaksi->barang->nama }}</td>
                <td>{{ $transaksi->jumlah_beli }}</td>
                <td>{{ $transaksi->satuan_beli }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer-content">
        <p><strong>Note:</strong> Barang yang diterima dalam keadaan baik dan lengkap.</p>
        <table class="signature-table">
            <tr>
                <th>PENERIMA</th>
                <th>PENGIRIM</th>
            </tr>
            <tr>
                <td>____________________</td>
                <td>____________________</td>
            </tr>
        </table>
    </div>
</body>

</html>
