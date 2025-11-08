<?php
require_once 'config/config.php';
requireRole(['admin', 'kasir']);

require_once 'models/Penjualan.php';

$database = new Database();
$db = $database->getConnection();

$penjualan = new Penjualan($db);

$penjualan_id = sanitizeInput($_GET['id']);
$penjualan->id = $penjualan_id;

if (!$penjualan->readOne()) {
    header('Location: penjualan.php');
    exit();
}

$detail_stmt = $penjualan->getDetailPenjualan($penjualan_id);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Penjualan - <?php echo APP_NAME; ?></title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .receipt {
            width: 300px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        
        .header p {
            margin: 0;
            font-size: 10px;
        }
        
        .transaction-info {
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .items {
            margin-bottom: 15px;
        }
        
        .item-header {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr;
            gap: 5px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 5px;
        }
        
        .item-row {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr 1fr;
            gap: 5px;
            margin-bottom: 3px;
        }
        
        .summary {
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .summary-row.total {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 5px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .receipt {
                border: none;
                width: 100%;
                max-width: 300px;
            }
            
            .no-print {
                display: none !important;
            }
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn">Cetak Struk</button>
        <a href="penjualan.php" class="btn">Transaksi Baru</a>
        <a href="dashboard.php" class="btn">Dashboard</a>
    </div>

    <div class="receipt">
        <div class="header">
            <h1><?php echo APP_NAME; ?></h1>
            <p>Dusun Rusia<br>
            Telp: (62) 896 4342 4632<br>
            Email: rusia@gmail.com</p>
        </div>
        
        <div class="transaction-info">
            <div class="info-row">
                <span>No. Transaksi:</span>
                <span><?php echo $penjualan->no_transaksi; ?></span>
            </div>
            <div class="info-row">
                <span>Tanggal:</span>
                <span><?php echo date('d/m/Y H:i:s', strtotime($penjualan->tanggal_penjualan)); ?></span>
            </div>
            <div class="info-row">
                <span>Kasir:</span>
                <span><?php echo $_SESSION['nama_lengkap']; ?></span>
            </div>
            <!-- TAMBAH INI -->
            <div class="info-row">
                <span>Customer:</span>
                <span><?php echo $penjualan->customer_id; ?></span>
            </div>
            <!-- SAMPAI SINI -->
        </div>
        
        <div class="items">
            <div class="item-header">
                <span>Item</span>
                <span>Qty</span>
                <span>Harga</span>
                <span>Total</span>
            </div>
            
            <?php 
            $subtotal = 0;
            while ($row = $detail_stmt->fetch(PDO::FETCH_ASSOC)): 
                $subtotal += $row['subtotal'];
            ?>
            <div class="item-row">
                <span><?php echo $row['nama_bahan']; ?></span>
                <span><?php echo $row['jumlah']; ?></span>
                <span><?php echo number_format($row['harga_satuan'], 0, ',', '.'); ?></span>
                <span><?php echo number_format($row['subtotal'], 0, ',', '.'); ?></span>
            </div>
            <?php endwhile; ?>

            
        </div>
        

           <?php 
// Subtotal sudah dihitung di atas

// Diskon otomatis 50%
$diskon_persen = 20;
$diskon = $subtotal * ($diskon_persen / 100);

// Total setelah diskon
$total_after_diskon = $subtotal - $diskon;

// PPN 12%
$ppn = $total_after_diskon * 0.10;

// Total akhir setelah PPN
$grand_total = $total_after_diskon + $ppn;

// Ambil uang bayar dari database
$bayar = $penjualan->total_bayar;

// Hitung kembalian
$kembalian = $bayar - $grand_total;
?>

<div class="summary">
    <div class="summary-row">
        <span>Subtotal:</span>
        <span>Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></span>
    </div>

    <div class="summary-row">
        <span>Diskon (<?php echo $diskon_persen; ?>%):</span>
        <span>Rp <?php echo number_format($diskon, 0, ',', '.'); ?></span>
    </div>

    <div class="summary-row">
        <span>Total Setelah Diskon:</span>
        <span>Rp <?php echo number_format($total_after_diskon, 0, ',', '.'); ?></span>
    </div>

    <div class="summary-row">
        <span>PPN (10%):</span>
        <span>Rp <?php echo number_format($ppn, 0, ',', '.'); ?></span>
    </div>

    <div class="summary-row total">
        <span>Total:</span>
        <span>Rp <?php echo number_format($grand_total, 0, ',', '.'); ?></span>
    </div>

    <div class="summary-row">
        <span>Bayar:</span>
        <span>Rp <?php echo number_format($bayar, 0, ',', '.'); ?></span>
    </div>

    <div class="summary-row">
        <span>Kembalian:</span>
        <span>Rp <?php echo number_format($kembalian, 0, ',', '.'); ?></span>
    </div>
    <div class="summary-row">
    <span>Catatan / Note</span>
    <span><?php echo htmlspecialchars($penjualan->note); ?></span>
</div>

</div>

        
        <div class="footer">
            <p>Terima kasih atas kunjungan Anda!</p>
            <p>bahan yang sudah dibeli tidak dapat dikembalikan</p>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() {
        //     window.print();
        // };
    </script>
</body>
</html>
