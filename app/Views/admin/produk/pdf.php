<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #333;
        margin: 40px;
    }

    h2, h4 {
        margin: 0;
        padding: 0;
    }

    .store-info {
        margin-bottom: 30px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    .store-info h2 {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
    }

    .store-info p {
        margin: 0;
        font-size: 14px;
        color: #555;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-size: 14px;
    }

    thead {
        background-color:white;
        color: black;
    }

    th, td {
        padding: 10px;
        border: 1px solid #ccc;
        text-align: left;
    }

    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tfoot td {
        font-weight: bold;
        background: #f0f0f0;
    }
            .footer {
            margin-top: 20px;
            text-align: right;
            font-style: italic;
        }
</style>

<div class="store-info">
    <h2>Toko ZETANI</h2>
    <p>Jl. Nasional, Semarang</p>
    <p>No. Telp: (000) 00000</p>
</div>

<h4>Laporan Data Produk</h4>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Harga</th>
            <th>Diskon (%)</th>
            <th>Harga Setelah Diskon</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produk as $p): ?>
            <tr>
                <td><?= esc($p['name']) ?></td>
                <td>Rp<?= number_format($p['price']) ?></td>
                <td><?= isset($p['diskon']) ? $p['diskon'] . '%' : '0%' ?></td>
                <td>
                    Rp<?php 
                        $diskon = isset($p['diskon']) ? $p['diskon'] : 0;
                        $hargaSetelahDiskon = $p['price'] - ($p['price'] * $diskon / 100);
                        echo number_format($hargaSetelahDiskon);
                    ?>
                </td>
                <td><?= $p['stock'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

  <div class="footer">
        Laporan ini dihasilkan secara otomatis.
    </div>
