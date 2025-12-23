# Script để sử dụng PHP từ C:\php8.3
# Chạy: .\use-php.ps1 hoặc source use-php.ps1

$phpPath = "C:\php8.3"
if (Test-Path "$phpPath\php.exe") {
    $env:PATH = "$phpPath;$env:PATH"
    Write-Host "✓ Đã cấu hình PATH để sử dụng PHP từ $phpPath" -ForegroundColor Green
    Write-Host "PHP version:" -ForegroundColor Cyan
    & "$phpPath\php.exe" -v
} else {
    Write-Host "✗ Không tìm thấy PHP tại $phpPath" -ForegroundColor Red
    exit 1
}



