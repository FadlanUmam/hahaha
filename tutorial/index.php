<?php
// Index tutorial - Menu utama tutorial PDF
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial Sistem POS Apotek - Menu Utama</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }
        
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 2.5em;
        }
        
        .subtitle {
            text-align: center;
            color: #7f8c8d;
            margin-bottom: 40px;
            font-style: italic;
        }
        
        .tutorial-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .tutorial-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        
        .tutorial-card:hover {
            border-color: #3498db;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(52, 152, 219, 0.2);
        }
        
        .tutorial-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.3em;
        }
        
        .tutorial-card p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .tutorial-card .features {
            list-style: none;
            padding: 0;
        }
        
        .tutorial-card .features li {
            padding: 5px 0;
            color: #27ae60;
            font-size: 0.9em;
        }
        
        .tutorial-card .features li:before {
            content: "✅ ";
            margin-right: 8px;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .info-section {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 20px;
            margin-top: 30px;
            border-left: 4px solid #2196f3;
        }
        
        .info-section h3 {
            color: #1976d2;
            margin-bottom: 15px;
        }
        
        .info-section ol {
            padding-left: 20px;
        }
        
        .info-section li {
            margin-bottom: 8px;
            color: #555;
        }
        
        .file-info {
            background: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📚 Tutorial Sistem POS Apotek</h1>
        <p class="subtitle">Panduan Lengkap Pembuatan Sistem Point of Sale dengan PHP PDO</p>
        
        <div class="tutorial-grid">
            <a href="tutorial.html" target="_blank" class="tutorial-card">
                <h3>📖 Tutorial Lengkap</h3>
                <p>Panduan detail step-by-step untuk pemula dengan penjelasan mendalam setiap komponen sistem.</p>
                <ul class="features">
                    <li>Penjelasan konsep MVC</li>
                    <li>Database design lengkap</li>
                    <li>Code examples detail</li>
                    <li>Troubleshooting guide</li>
                    <li>Best practices</li>
                </ul>
            </a>
            
            <a href="tutorial_singkat.html" target="_blank" class="tutorial-card">
                <h3>⚡ Tutorial Singkat</h3>
                <p>Referensi cepat untuk developer berpengalaman dengan fokus pada implementasi praktis.</p>
                <ul class="features">
                    <li>Quick start guide</li>
                    <li>Code snippets</li>
                    <li>Database schema</li>
                    <li>Feature checklist</li>
                    <li>Tips & tricks</li>
                </ul>
            </a>
        </div>
        
        <div class="action-buttons">
            <a href="tutorial.html" target="_blank" class="btn btn-primary">
                📖 Buka Tutorial Lengkap
            </a>
            <a href="tutorial_singkat.html" target="_blank" class="btn btn-secondary">
                ⚡ Buka Tutorial Singkat
            </a>
            <a href="generate_pdf.bat" class="btn btn-success">
                📄 Generate PDF (Windows)
            </a>
        </div>
        
        <div class="info-section">
            <h3>📄 Cara Generate PDF</h3>
            <ol>
                <li><strong>Browser Print (Recommended):</strong> Buka tutorial → Ctrl+P → Save as PDF</li>
                <li><strong>Windows Script:</strong> Jalankan generate_pdf.bat → Ikuti instruksi</li>
                <li><strong>mPDF Library:</strong> Install Composer → php generate_pdf.php</li>
                <li><strong>Online Converter:</strong> Upload HTML ke converter online</li>
            </ol>
        </div>
        
        <div class="file-info">
            <strong>📁 File Tutorial:</strong><br>
            • tutorial.html (19KB) - Tutorial lengkap<br>
            • tutorial_singkat.html (14KB) - Tutorial ringkas<br>
            • generate_pdf.php (4KB) - Script PDF generator<br>
            • generate_pdf.bat (816B) - Windows batch script<br>
            • README.md (4KB) - Dokumentasi tutorial
        </div>
        
        <div style="text-align: center; margin-top: 30px; color: #7f8c8d; font-size: 0.9em;">
            <p>🎯 <strong>Target:</strong> Developer PHP, Mahasiswa TI, Freelancer</p>
            <p>⏱️ <strong>Estimasi Waktu:</strong> 2-4 jam (tutorial lengkap) | 30-60 menit (tutorial singkat)</p>
            <p>🛠️ <strong>Skill Level:</strong> Pemula - Intermediate</p>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="../dashboard.php" style="color: #3498db; text-decoration: none;">
                ← Kembali ke Dashboard Sistem
            </a>
        </div>
    </div>
</body>
</html>
