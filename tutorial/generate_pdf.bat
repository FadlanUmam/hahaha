@echo off
echo ===============================================
echo   Generate PDF Tutorial Sistem POS Apotek
echo ===============================================
echo.

echo [1/3] Membuka tutorial di browser...
start "" "tutorial.html"

echo [2/3] Menunggu 3 detik...
timeout /t 3 /nobreak > nul

echo [3/3] Instruksi Generate PDF:
echo.
echo CARA GENERATE PDF:
echo 1. Di browser yang terbuka, tekan Ctrl+P
echo 2. Pilih "Save as PDF" sebagai destination
echo 3. Klik "Save" dan pilih lokasi penyimpanan
echo.
echo ATAU:
echo 1. Klik kanan pada halaman tutorial
echo 2. Pilih "Print..."
echo 3. Pilih "Save as PDF"
echo.
echo ===============================================
echo   Tutorial siap untuk di-convert ke PDF!
echo ===============================================
echo.
pause
