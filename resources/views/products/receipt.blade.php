<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk Penjualan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 12pt;
            margin: 0 0 5px 0;
        }
        .store-info {
            text-align: center;
            margin-bottom: 10px;
            font-size: 8pt;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .transaction-info {
            margin-bottom: 10px;
            font-size: 8pt;
        }
        table {
            width: 100%;
            margin-bottom: 10px;
            font-size: 8pt;
        }
        th, td {
            padding: 3px 0;
        }
        .amount-column {
            text-align: right;
        }
        .total-section {
            margin-top: 5px;
            text-align: right;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            font-size: 8pt;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SIMOTIF</h2>
        <div class="store-info">
            Sistem Informasi Motor Aktif<br>
            Jl. Contoh No. 123<br>
            Telp: (021) 1234567
        </div>
    </div>

    <div class="divider"></div>

    <div class="transaction-info">
        <table>
            <tr>
                <td>Tanggal</td>
                <td>: {{ $tanggal }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>: {{ $waktu }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td>: {{ $sale->user->name }}</td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <table>
        <tr>
            <th style="text-align: left">Item</th>
            <th style="text-align: right">Jml</th>
            <th style="text-align: right">Harga</th>
            <th style="text-align: right">Total</th>
        </tr>
        @php $grandTotal = 0; @endphp
        @foreach($sale->details as $detail)
            @php 
                $total = $detail->jumlah * $detail->harga;
                $grandTotal += $total;
            @endphp
            <tr>
                <td>{{ $detail->product->name }}</td>
                <td class="amount-column">{{ $detail->jumlah }}</td>
                <td class="amount-column">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                <td class="amount-column">{{ number_format($total, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <div class="total-section">
        <table>
            <tr>
                <td style="text-align: right"><strong>Total:</strong></td>
                <td style="text-align: right; width: 100px;">
                    <strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <div class="divider"></div>

    <div class="footer">
        Terima kasih atas kunjungan Anda<br>
        Barang yang sudah dibeli tidak dapat dikembalikan
    </div>
</body>
</html>