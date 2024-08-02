<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            /* Adjust font size for better fit */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 120mm;
            /* Set max width to typical receipt width */
            margin: 0 auto;
            margin-left: 0%;
            padding: 5px;
            /* Adjust padding for better fit */
        }

        .header,
        .footer {
            text-align: center;
        }

        .header {
            margin-bottom: 5px;
            /* Adjust margin for better fit */
        }

        .footer {
            margin-top: 5px;
            /* Adjust margin for better fit */
        }

        .content {
            margin-top: 5px;
            /* Adjust margin for better fit */
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border-bottom: 1px dashed rgb(88, 88, 88);
            padding: 2px;
            /* Adjust padding for better fit */
            text-align: left;
        }

        tfoot td {
            border-bottom: none;
            border-left: none;
        }

        tfoot tr td:first-child {
            border-right: none;
        }

        table th {
            border-right: 1px dashed rgb(88, 88, 88);
        }
        
        .table-invoice th:nth-child(2) {
            width: 50% ;
        } 
        
        .table-invoice tr td:nth-child(2){
            width: 50%;
        }

        .table-invoice tr td:last-child {
            padding-right: 8px;
            padding-left: 4px;
        }

        table td {
            border-right: 1px dashed rgb(88, 88, 88);
        }

        table th:last-child,
        table td:last-child {
            border-right: none;
        }

        .table-info {
            border-collapse: collapse;
            margin-bottom: 8px;
            /* Adjust margin for better fit */
        }

        .table-info td {
            border: none;
            padding: 2px;
        }

        .no-border {
            border: none;
        }

        h2,
        p {
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>{{ $store_name }}</h2>
            <p>{{ $store_address }}<br>Telepon: {{ $store_phone_number }}</p>
        </div>
        <div class="content">
            <table class="table-info">
                <tbody>
                    <tr>
                        <td>No. Nota: {{ $no_nota }}</td>
                        <td>{{date('d-m-Y')}}</td>
                    </tr>
                    <tr>
                        <td>Pelanggan: {{ $pelanggan }}</td>
                        <td>Kasir: {{ $kasir }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">Alamat: {{ $alamat }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">No. HP: {{ $no_telp }}</td>
                    </tr>
                </tbody>
            </table>
            <table class="table-invoice">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ $item['no'] }}</td>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td>{{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td>{{ number_format($item['amount'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Total Tagihan</td>
                        <td>{{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td colspan="2">Total Bayar</td>
                        <td>{{ number_format($tunai, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
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
