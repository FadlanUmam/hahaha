<?php
require_once 'config/config.php';
requireRole(['admin', 'gudang']);

require_once 'models/Bahan.php';
require_once 'models/KategoriBahan.php';

$database = new Database();
$db = $database->getConnection();

$bahan = new Bahan($db);
$kategori = new KategoriBahan($db);

$message = '';
$message_type = '';

// Handle form submission
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $bahan->kode_bahan = sanitizeInput($_POST['kode_bahan']);
                $bahan->nama_bahan = sanitizeInput($_POST['nama_bahan']);
                $bahan->kategori_id = sanitizeInput($_POST['kategori_id']);
                $bahan->satuan = sanitizeInput($_POST['satuan']);
                $bahan->harga_beli = sanitizeInput($_POST['harga_beli']);
                $bahan->harga_jual = sanitizeInput($_POST['harga_jual']);
                $bahan->stok = sanitizeInput($_POST['stok']);
                $bahan->stok_minimum = sanitizeInput($_POST['stok_minimum']);
                $bahan->deskripsi = sanitizeInput($_POST['deskripsi']);

                if ($bahan->create()) {
                    $message = 'Data bahan berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan data bahan!';
                    $message_type = 'error';
                }
                break;

            case 'update':
                $bahan->id = sanitizeInput($_POST['id']);
                $bahan->kode_bahan = sanitizeInput($_POST['kode_bahan']);
                $bahan->nama_bahan = sanitizeInput($_POST['nama_bahan']);
                $bahan->kategori_id = sanitizeInput($_POST['kategori_id']);
                $bahan->satuan = sanitizeInput($_POST['satuan']);
                $bahan->harga_beli = sanitizeInput($_POST['harga_beli']);
                $bahan->harga_jual = sanitizeInput($_POST['harga_jual']);
                $bahan->stok = sanitizeInput($_POST['stok']);
                $bahan->stok_minimum = sanitizeInput($_POST['stok_minimum']);
                $bahan->deskripsi = sanitizeInput($_POST['deskripsi']);

                if ($bahan->update()) {
                    $message = 'Data bahan berhasil diperbarui!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal memperbarui data bahan!';
                    $message_type = 'error';
                }
                break;

            case 'delete':
                $bahan->id = sanitizeInput($_POST['id']);
                if ($bahan->delete()) {
                    $message = 'Data bahan berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus data bahan!';
                    $message_type = 'error';
                }
                break;
        }
    }
}

// Get all bahan
$stmt = $bahan->readAll();

// Get all kategori for dropdown
$kategori_stmt = $kategori->readAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data bahan - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'kasir'): ?>
                <li class="nav-item">
                    <a href="penjualan.php" class="nav-link">
                        <i>🛒</i> Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="laporan_penjualan.php" class="nav-link">
                        <i>📈</i> Laporan Penjualan
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'gudang'): ?>
                <li class="nav-item">
                    <a href="bahan.php" class="nav-link active">
                        <i>👕</i> Data Bahan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="customer.php" class="nav-link">
                        <i>👤</i> Data Customer
                    </a>
                </li>
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
                        <i>🏷️</i> Kategori Bahan
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
                <h1>Data bahan</h1>
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

                <!-- Add bahan Form -->
                <div class="form-container">
                    <h2>Tambah bahan Baru</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kode_bahan">Kode bahan</label>
                                <input type="text" id="kode_bahan" name="kode_bahan" required>
                            </div>
                            <div class="form-group">
                                <label for="nama_bahan">Nama bahan</label>
                                <input type="text" id="nama_bahan" name="nama_bahan" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="kategori_id">Kategori</label>
                                <select id="kategori_id" name="kategori_id" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($row = $kategori_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                        <option value="<?php echo $row['id']; ?>"><?php echo $row['nama_kategori']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="satuan">Satuan</label>
                                <input type="text" id="satuan" name="satuan" required placeholder="placeholer">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="harga_beli">Harga Beli</label>
                                <input type="number" id="harga_beli" name="harga_beli" required min="0" step="100">
                            </div>
                            <div class="form-group">
                                <label for="harga_jual">Harga Jual</label>
                                <input type="number" id="harga_jual" name="harga_jual" required min="0" step="100">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="stok">Stok</label>
                                <input type="number" id="stok" name="stok" required min="0">
                            </div>
                            <div class="form-group">
                                <label for="stok_minimum">Stok Minimum</label>
                                <input type="number" id="stok_minimum" name="stok_minimum" required min="0">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah bahan</button>
                    </form>
                </div>

                <!-- Data bahan Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="table-title">Daftar bahan</h3>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama bahan</th>
                                <th>Kategori</th>
                                <th>Satuan</th>
                                <th>Harga Beli</th>
                                <th>Harga Jual</th>
                                <th>Stok</th>
                                <th>Stok Min</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo $row['kode_bahan']; ?></td>
                                <td><?php echo $row['nama_bahan']; ?></td>
                                <td><?php echo $row['nama_kategori']; ?></td>
                                <td><?php echo $row['satuan']; ?></td>
                                <td><?php echo formatCurrency($row['harga_beli']); ?></td>
                                <td><?php echo formatCurrency($row['harga_jual']); ?></td>
                                <td>
                                    <span class="badge <?php echo $row['stok'] <= $row['stok_minimum'] ? 'badge-danger' : 'badge-success'; ?>">
                                        <?php echo $row['stok']; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['stok_minimum']; ?></td>
                                <td>
                                    <button onclick="editbahan(<?php echo htmlspecialchars(json_encode($row)); ?>)" 
                                            class="btn btn-warning btn-sm">Edit</button>
                                    <button onclick="deletebahan(<?php echo $row['id']; ?>)" 
                                            class="btn btn-danger btn-sm">Hapus</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; width: 90%; max-width: 600px; max-height: 90%; overflow-y: auto;">
            <h2>Edit bahan</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_kode_bahan">Kode bahan</label>
                        <input type="text" id="edit_kode_bahan" name="kode_bahan" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_nama_bahan">Nama bahan</label>
                        <input type="text" id="edit_nama_bahan" name="nama_bahan" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_kategori_id">Kategori</label>
                        <select id="edit_kategori_id" name="kategori_id" required>
                            <?php 
                            $kategori_stmt = $kategori->readAll();
                            while ($row = $kategori_stmt->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_satuan">Satuan</label>
                        <input type="text" id="edit_satuan" name="satuan" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_harga_beli">Harga Beli</label>
                        <input type="number" id="edit_harga_beli" name="harga_beli" required min="0" step="100">
                    </div>
                    <div class="form-group">
                        <label for="edit_harga_jual">Harga Jual</label>
                        <input type="number" id="edit_harga_jual" name="harga_jual" required min="0" step="100">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="edit_stok">Stok</label>
                        <input type="number" id="edit_stok" name="stok" required min="0">
                    </div>
                    <div class="form-group">
                        <label for="edit_stok_minimum">Stok Minimum</label>
                        <input type="number" id="edit_stok_minimum" name="stok_minimum" required min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_deskripsi">Deskripsi</label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editbahan(data) {
            document.getElementById('edit_id').value = data.id;
            document.getElementById('edit_kode_bahan').value = data.kode_bahan;
            document.getElementById('edit_nama_bahan').value = data.nama_bahan;
            document.getElementById('edit_kategori_id').value = data.kategori_id;
            document.getElementById('edit_satuan').value = data.satuan;
            document.getElementById('edit_harga_beli').value = data.harga_beli;
            document.getElementById('edit_harga_jual').value = data.harga_jual;
            document.getElementById('edit_stok').value = data.stok;
            document.getElementById('edit_stok_minimum').value = data.stok_minimum;
            document.getElementById('edit_deskripsi').value = data.deskripsi;
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function deletebahan(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data bahan ini?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        document.getElementById('editModal').onclick = function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
