<?php
require_once 'config/config.php';
requireRole(['admin', 'kasir']);

require_once 'models/Bahan.php';
require_once 'models/Customer.php'; //TAMBAH INI
require_once 'models/Penjualan.php';

$database = new Database();
$db = $database->getConnection();

$bahan = new Bahan($db);
$customer = new Customer($db); //TAMBAH INI
$penjualan = new Penjualan($db);

$message = '';
$message_type = '';

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'search_bahan':
            $keyword = sanitizeInput($_GET['keyword']);
            $stmt = $bahan->search($keyword);
            $results = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = $row;
            }
            echo json_encode($results);
            exit;
            
        case 'get_bahan':
            $bahan_id = sanitizeInput($_GET['bahan_id']);
            $bahan->id = $bahan_id;
            if ($bahan->readOne()) {
                echo json_encode([
                    'id' => $bahan->id,
                    'kode_bahan' => $bahan->kode_bahan,
                    'nama_bahan' => $bahan->nama_bahan,
                    'harga_jual' => $bahan->harga_jual,
                    'stok' => $bahan->stok
                ]);
            } else {
                echo json_encode(['error' => 'bahan tidak ditemukan']);
            }
            exit;
    }
}

// Handle transaction submission
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'process_transaction') {
    try {
        $db->beginTransaction();
        
        // Create penjualan record
        $penjualan->no_transaksi = $penjualan->generateNoTransaksi();
        $penjualan->user_id = $_SESSION['user_id'];
        $penjualan->customer_id = sanitizeInput($_POST['customer_id']); //TAMBAH INI
        $penjualan->total_harga = sanitizeInput($_POST['total_harga']);
         $penjualan->diskon = sanitizeInput($_POST['diskon']);
        $penjualan->total_bayar = sanitizeInput($_POST['total_bayar']);
        $penjualan->note = sanitizeInput($_POST['note']); // simpan note
        $penjualan->kembalian = sanitizeInput($_POST['kembalian']);
        
        if (!$penjualan->create()) {
            throw new Exception('Gagal membuat transaksi');
        }
        
        $penjualan_id = $db->lastInsertId();
        
        // Process detail penjualan
        $items = json_decode($_POST['items'], true);
        foreach ($items as $item) {
            // Insert detail penjualan
            $detail_query = "INSERT INTO detail_penjualan (penjualan_id, bahan_id, jumlah, harga_satuan, subtotal) 
                             VALUES (:penjualan_id, :bahan_id, :jumlah, :harga_satuan, :subtotal)";
            $detail_stmt = $db->prepare($detail_query);
            $detail_stmt->bindParam(':penjualan_id', $penjualan_id);
            $detail_stmt->bindParam(':bahan_id', $item['id']);
            $detail_stmt->bindParam(':jumlah', $item['quantity']);
            $detail_stmt->bindParam(':harga_satuan', $item['price']);
            $detail_stmt->bindParam(':subtotal', $item['subtotal']);
            
            if (!$detail_stmt->execute()) {
                throw new Exception('Gagal menyimpan detail penjualan');
            }
            
            // Update stok bahan
            $bahan->updateStok($item['id'], -$item['quantity']);
        }
        
        $db->commit();
        
        // Redirect to receipt
        header('Location: struk.php?id=' . $penjualan_id);
        exit();
        
    } catch (Exception $e) {
        $db->rollBack();
        $message = 'Error: ' . $e->getMessage();
        $message_type = 'error';
    }
}
$customer_stmt = $customer->readAll(); //TAMBAH INI
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjualan - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .pos-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
            height: calc(100vh - 120px);
        }
        
        .product-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            overflow-y: auto;
        }
        
        .cart-section {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        
        .search-box {
            margin-bottom: 20px;
        }
        
        .search-box input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .product-card {
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .product-card:hover {
            border-color: #3498db;
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.2);
        }
        
        .product-card.selected {
            border-color: #27ae60;
            background: #f8fff8;
        }
        
        .product-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .product-price {
            color: #27ae60;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .product-stock {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .cart-items {
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
        }
        
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e1e1e1;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .item-info {
            flex: 1;
        }
        
        .item-name {
            font-weight: 500;
            margin-bottom: 2px;
        }
        
        .item-price {
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-control button {
            width: 25px;
            height: 25px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            cursor: pointer;
            border-radius: 3px;
        }
        
        .quantity-control input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            padding: 2px;
        }
        
        .cart-summary {
            border-top: 2px solid #e1e1e1;
            padding-top: 20px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-row.total {
            font-weight: bold;
            font-size: 18px;
            color: #2c3e50;
            border-top: 1px solid #e1e1e1;
            padding-top: 10px;
            margin-top: 10px;
        }
        
        .payment-section {
            margin-top: 20px;
        }
        
        .payment-section input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
        }
        
        .btn-process {
            width: 100%;
            padding: 15px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .btn-process:hover {
            background: #229954;
        }
        
        .btn-process:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2><?php echo APP_NAME; ?></h2>
            </div>
            
            <ul class="sidebar-nav">
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link">
                        <i>📊</i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="penjualan.php" class="nav-link active">
                        <i>🛒</i> Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan_penjualan.php" class="nav-link">
                        <i>📈</i> Laporan Penjualan
                    </a>
                </li>
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'gudang'): ?>
                <li class="nav-item">
                    <a href="bahan.php" class="nav-link">
                        <i>👕</i> Data bahan
                    </a>
                </li>
                <!-- TAMBAH INI -->
                <li class="nav-item">
                    <a href="customer.php" class="nav-link">
                        <i>👤</i> Data Customer
                    </a>
                </li>
                <!-- SAMPAI SINI -->
                <li class="nav-item">
                    <a href="pembelian.php" class="nav-link">
                        <i>📦</i> Pembelian
                    </a>
                </li>
                <li class="nav-item">
                    <a href="stok.php" class="nav-link">
                        <i>📋</i> Manajemen Stok
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a href="kategori.php" class="nav-link">
                        <i>🏷️</i> Kategori bahan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="vendor.php" class="nav-link">
                        <i>🏢</i> Vendor
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users.php" class="nav-link">
                        <i>👥</i> Manajemen User
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="logout.php" class="nav-link">
                        <i>🚪</i> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Navigation -->
            <header class="top-nav">
                <h1>Penjualan</h1>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></div>
                        <div class="user-role"><?php echo ucfirst($_SESSION['user_role']); ?></div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="content">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <div class="pos-container">
                    <!-- Product Section -->
                    <div class="product-section">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Cari bahan..." onkeyup="searchProducts()">
                        </div>
                        
                        <div id="productGrid" class="product-grid">
                            <!-- Products will be loaded here -->
                        </div>
                    </div>

                    <!-- Cart Section -->
                    <div class="cart-section">
                        <h3>Keranjang Belanja</h3>
                        
                        <div class="cart-items" id="cartItems">
                            <p style="text-align: center; color: #7f8c8d; padding: 20px;">
                                Keranjang kosong
                            </p>
                        </div>
                        
                            <div class="summary-row">
                  <span>Diskon (20%):</span>
                      <span id="diskonValue"></span>
                        </div>
                        <div class="cart-summary">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="subtotal">Rp 0</span>
                            </div>
                            <div class="summary-row">
                                <span>PPN (10%):</span>
                                <span id="ppn">Rp 0</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="total">Rp 0</span>
                            </div>





                           
                        
                        
                        <!-- TAMBAH INI -->
                        <div class="form-group">
                            <label for="customer_id">Customer:</label>
                            <select id="customer_id" name="customer_id" required>
                                <option value="">Pilih Customer</option>
                                <?php while ($row = $customer_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo $row['nama_customer']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
<!-- Note / Catatan -->
<div class="form-group">
    <label for="note">Catatan / Note:</label>
    <textarea id="note" name="note" placeholder="Tambahkan catatan..." style="width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ddd;"></textarea>
</div>



                        <!-- SAMPAI SINI -->
                        <div class="payment-section">
                            <input type="number" id="paymentInput" placeholder="Jumlah Bayar" onkeyup="calculateChange()">
                            <div class="summary-row">
                                <span>Kembalian:</span>
                                <span id="change">Rp 0</span>
                            </div>
                            <button class="btn-process" id="processBtn" onclick="processTransaction()" disabled>
                                Proses Transaksi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let cart = [];
        let products = [];

        // Load products on page load
        window.onload = function() {
            searchProducts();
        };

        function searchProducts() {
            const keyword = document.getElementById('searchInput').value;
            
            fetch(`penjualan.php?action=search_bahan&keyword=${encodeURIComponent(keyword)}`)
                .then(response => response.json())
                .then(data => {
                    products = data;
                    displayProducts(data);
                })
                .catch(error => console.error('Error:', error));
        }

        function displayProducts(products) {
            const grid = document.getElementById('productGrid');
            grid.innerHTML = '';

            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';
                productCard.onclick = () => addToCart(product);
                
                productCard.innerHTML = `
                    <div class="product-name">${product.nama_bahan}</div>
                    <div class="product-price">${formatCurrency(product.harga_jual)}</div>
                    <div class="product-stock">Stok: ${product.stok} ${product.satuan}</div>
                `;
                
                grid.appendChild(productCard);
            });
        }

        function addToCart(product) {
            if (product.stok <= 0) {
                alert('Stok bahan habis!');
                return;
            }

            const existingItem = cart.find(item => item.id === product.id);
            
            if (existingItem) {
                if (existingItem.quantity < product.stok) {
                    existingItem.quantity++;
                } else {
                    alert('Stok tidak mencukupi!');
                    return;
                }
            } else {
                cart.push({
                    id: product.id,
                    kode_bahan: product.kode_bahan,
                    nama_bahan: product.nama_bahan,
                    price: parseFloat(product.harga_jual),
                    quantity: 1,
                    max_stock: product.stok
                });
            }
            
            updateCartDisplay();
        }

        function updateCartDisplay() {
            const cartItems = document.getElementById('cartItems');
            
            if (cart.length === 0) {
                cartItems.innerHTML = '<p style="text-align: center; color: #7f8c8d; padding: 20px;">Keranjang kosong</p>';
                updateSummary();
                return;
            }

            cartItems.innerHTML = '';
            
            cart.forEach((item, index) => {
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                
                cartItem.innerHTML = `
                    <div class="item-info">
                        <div class="item-name">${item.nama_bahan}</div>
                        <div class="item-price">${formatCurrency(item.price)}</div>
                    </div>
                    <div class="item-controls">
                        <div class="quantity-control">
                            <button onclick="updateQuantity(${index}, -1)">-</button>
                            <input type="number" value="${item.quantity}" min="1" max="${item.max_stock}" 
                                   onchange="setQuantity(${index}, this.value)">
                            <button onclick="updateQuantity(${index}, 1)">+</button>
                        </div>
                        <button onclick="removeFromCart(${index})" class="btn btn-danger btn-sm">Hapus</button>
                    </div>
                `;
                
                cartItems.appendChild(cartItem);
            });
            
            updateSummary();
        }

        function updateQuantity(index, change) {
            const item = cart[index];
            const newQuantity = item.quantity + change;
            
            if (newQuantity >= 1 && newQuantity <= item.max_stock) {
                item.quantity = newQuantity;
                updateCartDisplay();
            }
        }

        function setQuantity(index, value) {
            const item = cart[index];
            const newQuantity = parseInt(value);
            
            if (newQuantity >= 1 && newQuantity <= item.max_stock) {
                item.quantity = newQuantity;
                updateCartDisplay();
            }
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartDisplay();
        }

      function updateSummary() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    // Diskon 50%
    const diskonPersen = 20;
    const diskonValue = (subtotal * diskonPersen) / 100;

    // Hitung subtotal setelah diskon dulu
    const subtotalAfterDiskon = subtotal - diskonValue;

    // PPN 10% DARI HARGA SETELAH DISKON ✅
    const ppn = subtotalAfterDiskon * 0.10;

    // Total akhir
    const total = subtotalAfterDiskon + ppn;

    document.getElementById('subtotal').textContent = formatCurrency(subtotal);
    document.getElementById('diskonValue').textContent = formatCurrency(diskonValue);
    document.getElementById('ppn').textContent = formatCurrency(ppn);
    document.getElementById('total').textContent = formatCurrency(total);

    // Simpan biar calculateChange tahu totalnya
    document.getElementById('total').setAttribute("data-total", total);

    calculateChange();
}

function calculateChange() {
    const total = parseFloat(document.getElementById('total').getAttribute("data-total")) || 0;
    const payment = parseFloat(document.getElementById('paymentInput').value) || 0;

    const change = payment - total;
    document.getElementById('change').textContent = formatCurrency(change > 0 ? change : 0);

    // Tombol aktif kalau bayar cukup
    document.getElementById('processBtn').disabled = (cart.length === 0 || payment < total);
}



  function processTransaction() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

    const diskonPersen = 20;
    const diskonValue = (subtotal * diskonPersen) / 100;

    const subtotalAfterDiskon = subtotal - diskonValue;
    const ppn = subtotalAfterDiskon * 0.10;
    const total = subtotalAfterDiskon + ppn;

    const payment = parseFloat(document.getElementById('paymentInput').value);
    const change = payment - total;
    const customer_id = document.getElementById('customer_id').value;
    const note = document.getElementById('note').value; // ✅ ambil note di sini

    if (payment < total) {
        alert('Jumlah bayar kurang!');
        return;
    }

    cart.forEach(item => {
        item.subtotal = item.price * item.quantity;
    });

    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="action" value="process_transaction">
        <input type="hidden" name="items" value='${JSON.stringify(cart)}'>
        <input type="hidden" name="total_harga" value="${total}">
        <input type="hidden" name="customer_id" value="${customer_id}">
        <input type="hidden" name="total_bayar" value="${payment}">
        <input type="hidden" name="kembalian" value="${change}">
        <input type="hidden" name="diskon" value="${diskonValue}">
        <input type="hidden" name="diskon_persen" value="${diskonPersen}">
        <input type="hidden" name="note" value="${note}">
    `;

    document.body.appendChild(form);
    form.submit();
}



        function formatCurrency(amount) {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        }
    </script>
</body>
</html>
