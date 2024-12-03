<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Resi Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            font-size: 12pt;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin-bottom: 5px;
            font-size: 18pt;
        }
        .header h3 {
            margin-top: 0;
            font-size: 14pt;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 10pt;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .amount {
            text-align: right;
        }
        .summary {
            margin-top: 30px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        .summary table {
            width: 300px;
            float: right;
            margin-bottom: 40px;
        }
        .summary table td {
            border: none;
            padding: 5px;
        }
        .summary table td:first-child {
            text-align: left;
        }
        .summary table td:last-child {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px;
            font-size: 10pt;
            border-top: 1px solid #000;
        }
        .clear {
            clear: both;
        }
        /* Style for the total row */
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        /* Style for alternating rows */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>SIMOTIF</h2>
        <h3>Resi Penjualan</h3>
    </div>

    <div class="info">
        <p><strong>Tanggal:</strong> {{ $tanggal }}</p>
        <p><strong>Waktu:</strong> {{ $waktu }}</p>
        <p><strong>Kasir:</strong> {{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</p>
        @if(request('date'))
            <p><strong>Filter Tanggal:</strong> {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}</p>
        @endif
        @if(request('search'))
            <p><strong>Pencarian:</strong> {{ request('search') }}</p>
        @endif
    </div>

    @if($produkTerjual->isNotEmpty())
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Nama Produk</th>
                <th style="width: 15%;">Brand</th>
                <th style="width: 10%;">Jumlah</th>
                <th style="width: 15%;">Harga</th>
                <th style="width: 15%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $totalItems = 0;
                $totalAmount = 0;
                $uniqueProducts = [];
            @endphp
            
            @foreach($produkTerjual as $index => $sale)
                @php
                    $totalItems += $sale->jumlah;
                    $itemTotal = $sale->jumlah * $sale->harga;
                    $totalAmount += $itemTotal;
                    $uniqueProducts[$sale->product_id] = true;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->sale->tanggal_jual)->format('d/m/Y') }}</td>
                    <td>{{ $sale->product->name }}</td>
                    <td>{{ $sale->product->brand }}</td>
                    <td class="amount">{{ number_format($sale->jumlah, 0, ',', '.') }}</td>
                    <td class="amount">Rp {{ number_format($sale->harga, 0, ',', '.') }}</td>
                    <td class="amount">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <table>
            <tr>
                <td>Total Transaksi</td>
                <td>{{ $produkTerjual->count() }}</td>
            </tr>
            <tr>
                <td>Total Produk Terjual</td>
                <td>{{ $totalItems }}</td>
            </tr>
            <tr>
                <td>Total Jenis Produk</td>
                <td>{{ count($uniqueProducts) }}</td>
            </tr>
            <tr>
                <td><strong>Total Pembayaran</strong></td>
                <td><strong>Rp {{ number_format($totalAmount, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 20px;">
        <p>Tidak ada data penjualan untuk periode ini</p>
    </div>
    @endif

    <div class="clear"></div>

    <div class="footer">
        <p>SIMOTIF - Sistem Informasi Motor Aktif</p>
        <p>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>