$path = 'D:\websie\dhaka-magazine\backend\storage\framework\views\38a4eb0dad73c3e0ccb71fcb128546db.php'
if (!(Test-Path $path)) {
    Write-Host 'NOT FOUND'
    exit
}
$lines = [System.IO.File]::ReadAllLines($path, [System.Text.Encoding]::UTF8)
Write-Host "Total compiled lines: $($lines.Count)"
$start = [Math]::Max(0, $lines.Count - 30)
for ($i = $start; $i -lt $lines.Count; $i++) {
    Write-Host "$($i+1): $($lines[$i])"
}
