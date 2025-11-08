<?php
// Script untuk generate PDF dari HTML tutorial
// Pastikan library mPDF sudah terinstall: composer require mpdf/mpdf

require_once 'vendor/autoload.php'; // Jika menggunakan Composer

// Atau download mPDF manual dan include:
// require_once 'mpdf/vendor/autoload.php';

try {
    // Baca file HTML tutorial
    $html = file_get_contents('tutorial.html');
    
    // Konfigurasi mPDF
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 16,
        'margin_bottom' => 16,
        'margin_header' => 9,
        'margin_footer' => 9,
        'tempDir' => sys_get_temp_dir()
    ]);
    
    // Set header dan footer
    $mpdf->SetHTMLHeader('
        <div style="text-align: center; font-size: 10px; color: #666;">
            Tutorial Sistem POS Apotek
        </div>
    ');
    
    $mpdf->SetHTMLFooter('
        <div style="text-align: center; font-size: 10px; color: #666;">
            Halaman {PAGENO} dari {nbpg}
        </div>
    ');
    
    // Write HTML content
    $mpdf->WriteHTML($html);
    
    // Output PDF
    $mpdf->Output('Tutorial_Sistem_POS_Apotek.pdf', 'D'); // Download
    // $mpdf->Output('Tutorial_Sistem_POS_Apotek.pdf', 'I'); // View in browser
    // $mpdf->Output('tutorial/Tutorial_Sistem_POS_Apotek.pdf', 'F'); // Save to file
    
} catch (Exception $e) {
    echo "Error generating PDF: " . $e->getMessage();
}

// Alternatif tanpa mPDF - menggunakan browser print to PDF
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate PDF Tutorial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .option {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>📄 Generate PDF Tutorial</h1>
    
    <div class="option">
        <h3>Opsi 1: Print to PDF (Browser)</h3>
        <p>Cara termudah - gunakan fitur print browser:</p>
        <ol>
            <li>Buka file <a href="tutorial.html" target="_blank">tutorial.html</a></li>
            <li>Tekan Ctrl+P (Windows) atau Cmd+P (Mac)</li>
            <li>Pilih "Save as PDF" sebagai destination</li>
            <li>Klik Save</li>
        </ol>
        <a href="tutorial.html" target="_blank" class="btn">Buka Tutorial HTML</a>
    </div>
    
    <div class="option">
        <h3>Opsi 2: Menggunakan mPDF Library</h3>
        <p>Untuk generate PDF otomatis dengan PHP:</p>
        <ol>
            <li>Install Composer: <code>composer install</code></li>
            <li>Install mPDF: <code>composer require mpdf/mpdf</code></li>
            <li>Jalankan script generate_pdf.php</li>
        </ol>
        <a href="generate_pdf.php" class="btn">Generate PDF dengan mPDF</a>
    </div>
    
    <div class="option">
        <h3>Opsi 3: Online HTML to PDF Converter</h3>
        <p>Gunakan layanan online seperti:</p>
        <ul>
            <li><a href="https://html-pdf-api.com/" target="_blank">HTML PDF API</a></li>
            <li><a href="https://www.ilovepdf.com/html-to-pdf" target="_blank">ILovePDF</a></li>
            <li><a href="https://pdfcrowd.com/" target="_blank">PDFCrowd</a></li>
        </ul>
    </div>
    
    <div class="option">
        <h3>Opsi 4: Chrome Headless (Advanced)</h3>
        <p>Untuk developer yang ingin otomatisasi:</p>
        <code>
            # Install Chrome headless<br>
            chrome --headless --disable-gpu --print-to-pdf=tutorial.pdf tutorial.html
        </code>
    </div>
    
    <div style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; padding: 15px; margin: 20px 0;">
        <strong>💡 Rekomendasi:</strong> Gunakan Opsi 1 (Browser Print) untuk kemudahan dan kualitas terbaik!
    </div>
</body>
</html>
