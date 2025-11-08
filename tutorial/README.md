# 📚 Tutorial Sistem POS Apotek

Panduan lengkap pembuatan sistem Point of Sale untuk apotek menggunakan PHP PDO dengan hak akses multi-role.

## 📁 File Tutorial

### 1. **tutorial.html** - Tutorial Lengkap
- Tutorial detail dengan penjelasan step-by-step
- Cocok untuk pemula yang ingin memahami setiap aspek
- Berisi kode lengkap dan penjelasan mendalam

### 2. **tutorial_singkat.html** - Tutorial Ringkas
- Versi singkat untuk referensi cepat
- Fokus pada poin-poin penting
- Cocok untuk yang sudah familiar dengan PHP

### 3. **generate_pdf.php** - Script Generate PDF
- Script PHP untuk generate PDF otomatis
- Memerlukan library mPDF
- Alternatif untuk generate PDF programmatically

### 4. **generate_pdf.bat** - Batch Script Windows
- Script Windows untuk membuka tutorial
- Instruksi cara print to PDF
- Mudah digunakan tanpa install library tambahan

## 🚀 Cara Generate PDF

### Opsi 1: Browser Print (Recommended)
1. Buka file `tutorial.html` atau `tutorial_singkat.html`
2. Tekan **Ctrl+P** (Windows) atau **Cmd+P** (Mac)
3. Pilih **"Save as PDF"** sebagai destination
4. Klik **Save** dan pilih lokasi penyimpanan

### Opsi 2: Menggunakan Batch Script
1. Double-click file `generate_pdf.bat`
2. Ikuti instruksi yang muncul di command prompt
3. Browser akan terbuka dengan tutorial
4. Gunakan Ctrl+P untuk print to PDF

### Opsi 3: mPDF Library (Advanced)
1. Install Composer: `composer install`
2. Install mPDF: `composer require mpdf/mpdf`
3. Jalankan: `php generate_pdf.php`

### Opsi 4: Online Converter
1. Upload file HTML ke converter online:
   - https://html-pdf-api.com/
   - https://www.ilovepdf.com/html-to-pdf
   - https://pdfcrowd.com/

## 📋 Isi Tutorial

### Tutorial Lengkap (tutorial.html)
- **Persiapan Environment** - Setup XAMPP dan tools
- **Database Design** - Perancangan schema database
- **Project Structure** - Organisasi folder dan file
- **Database Connection** - Konfigurasi PDO
- **Models Creation** - Pembuatan model PHP
- **Authentication** - Sistem login dan session
- **UI/UX Design** - CSS styling dan layout
- **CRUD Operations** - Create, Read, Update, Delete
- **POS System** - Interface kasir dengan JavaScript
- **Stock Management** - Manajemen stok obat
- **Reporting** - Sistem laporan
- **Testing & Deploy** - Testing dan deployment

### Tutorial Singkat (tutorial_singkat.html)
- **Quick Start Guide** - Langkah cepat
- **Code Snippets** - Potongan kode penting
- **Database Schema** - Tabel dan relasi
- **Default Accounts** - Akun default sistem
- **Feature Overview** - Ringkasan fitur
- **Tips & Tricks** - Tips pengembangan

## 🎯 Target Pembaca

### Tutorial Lengkap
- ✅ Pemula dalam PHP
- ✅ Yang ingin memahami konsep MVC
- ✅ Developer yang belajar PDO
- ✅ Yang ingin belajar sistem multi-role

### Tutorial Singkat
- ✅ Developer berpengalaman
- ✅ Yang butuh referensi cepat
- ✅ Untuk review dan checklist
- ✅ Dokumentasi proyek

## 🛠️ Tools yang Digunakan

- **PHP 7.4+** - Server-side scripting
- **MySQL 5.7+** - Database management
- **PDO** - Database abstraction layer
- **HTML5** - Markup structure
- **CSS3** - Styling dan layout
- **JavaScript ES6** - Client-side functionality
- **XAMPP** - Development environment

## 📊 Struktur Database

```
pos_apotek/
├── users (autentikasi)
├── kategori_obat (kategori)
├── obat (data obat)
├── supplier (pemasok)
├── penjualan (transaksi)
└── detail_penjualan (item transaksi)
```

## 🔐 Hak Akses

- **Admin**: Full access ke semua fitur
- **Kasir**: POS, penjualan, laporan
- **Gudang**: Obat, pembelian, stok

## 💡 Tips Penggunaan

1. **Untuk Pemula**: Mulai dengan tutorial lengkap
2. **Untuk Experienced**: Gunakan tutorial singkat sebagai referensi
3. **Print Quality**: Gunakan browser print untuk kualitas terbaik
4. **Offline Reading**: Save PDF untuk dibaca offline
5. **Customization**: Edit HTML untuk menyesuaikan konten

## 🆘 Troubleshooting

### PDF tidak terbuka
- Pastikan browser support print to PDF
- Coba gunakan browser lain (Chrome, Firefox, Edge)

### Script mPDF error
- Pastikan Composer sudah terinstall
- Check PHP version compatibility
- Install mPDF dengan benar

### HTML tidak render
- Pastikan file HTML valid
- Check encoding UTF-8
- Buka dengan browser modern

## 📞 Support

Jika mengalami kesulitan:
1. Check troubleshooting di atas
2. Pastikan semua file tutorial ada
3. Gunakan browser terbaru
4. Coba opsi generate PDF yang berbeda

---

**Selamat belajar dan mengembangkan sistem POS Apotek!** 🎉
