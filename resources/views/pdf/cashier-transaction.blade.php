<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header {
            margin-bottom: 20px;
        }

        .footer {
            margin-top: 20px;
        }

        .content {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }
        /* Target the table with class "table-info" */
        .table-info {
            border-collapse: collapse; /* Ensure no space between table cells */
            border: none; /* Remove the outer border */
            margin-bottom: 16px;
        }

        .table-info td {
            border: none; /* Remove the borders from table cells */
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>UD. PURSIDA</h2>
            <p>Jl. Asahan Km. VI, Depan Polres<br>Telepon: 087776827032</p>
        </div>
        <div class="content">
            <table class="table-info">
                <tbody>
                    <tr>
                        <td>
                            No. Nota: {{ $no_nota }}
                        </td>
                    </tr>
                    <tr>
                        <td>Kasir : {{ $kasir }}</td>
                        <td>Alamat : {{ $alamat }}</td>
                    </tr>
                    <tr>
                        <td>Pelanggan : {{ $pelanggan }}</td>
                        <td>No. HP : {{ $no_telp }}</td>
                    </tr>
                </tbody>
            </table>
            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>{{ number_format($item['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">Subtotal</td>
                        <td>{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Diskon (%)</td>
                        <td>{{ number_format($diskon, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Total</td>
                        <td>{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Tunai</td>
                        <td>{{ number_format($tunai, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Kembalian</td>
                        <td>{{ number_format($kembalian, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="footer">
            <p>***Terimakasih Atas Kunjungan Anda***</p>
        </div>
    </div>
</body>

</html>
