CREATE DATABASE IF NOT EXISTS pos_konveksi;
USE pos_konveksi;

-- Tabel Users (Admin, Kasir, Gudang)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'kasir', 'gudang') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori bahan
CREATE TABLE kategori_bahan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel bahan
CREATE TABLE bahan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_bahan VARCHAR(20) UNIQUE NOT NULL,
    nama_bahan VARCHAR(200) NOT NULL,
    kategori_id INT,
    satuan VARCHAR(20) NOT NULL,
    harga_beli DECIMAL(10,2) NOT NULL,
    harga_jual DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    stok_minimum INT DEFAULT 0,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori_bahan(id) ON DELETE SET NULL
);

-- Tabel vendor
CREATE TABLE vendor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_vendor VARCHAR(200) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel customer
CREATE TABLE customer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_customer VARCHAR(200) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Pembelian (Masuk dari Gudang)
CREATE TABLE pembelian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_faktur VARCHAR(50) UNIQUE NOT NULL,
    vendor_id INT,
    user_id INT,
    total_harga DECIMAL(12,2) NOT NULL,
    tanggal_pembelian DATE NOT NULL,
    status ENUM('pending', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendor_id) REFERENCES vendor(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel Detail Pembelian
CREATE TABLE detail_pembelian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pembelian_id INT,
    bahan_id INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (pembelian_id) REFERENCES pembelian(id) ON DELETE CASCADE,
    FOREIGN KEY (bahan_id) REFERENCES bahan(id) ON DELETE CASCADE
);

-- Tabel Penjualan (Kasir)
CREATE TABLE penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_transaksi VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    customer_id INT,
    diskon DECIMAL(12,2) NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    total_bayar DECIMAL(12,2) NOT NULL,
    kembalian DECIMAL(12,2) NOT NULL,
    note TEXT,
    tanggal_penjualan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabel Detail Penjualan
CREATE TABLE detail_penjualan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    penjualan_id INT,
    bahan_id INT,
    jumlah INT NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (penjualan_id) REFERENCES penjualan(id) ON DELETE CASCADE,
    FOREIGN KEY (bahan_id) REFERENCES bahan(id) ON DELETE CASCADE
);

-- Insert data awal
INSERT INTO users (username, password, nama_lengkap, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@apotek.com', 'admin'),
('kasir1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kasir Utama', 'kasir@apotek.com', 'kasir'),
('gudang1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Gudang', 'gudang@apotek.com', 'gudang');

INSERT INTO kategori_bahan (nama_kategori, deskripsi) VALUES
('bahan Bebas', 'bahan yang dapat dibeli tanpa resep dokter'),
('bahan Keras', 'bahan yang memerlukan resep dokter'),
('bahan Herbal', 'bahan tradisional berbahan herbal'),
('Alat Kesehatan', 'Peralatan medis dan kesehatan'),
('Vitamin & Suplemen', 'Vitamin dan suplemen kesehatan');

INSERT INTO vendor (nama_vendor, alamat, telepon, email) VALUES
('PT. Farmasi Sehat', 'Jl. Raya Jakarta No. 123', '021-1234567', 'info@farmasehat.com'),
('CV. Medika Jaya', 'Jl. Sudirman No. 456', '021-7654321', 'contact@medikajaya.com');