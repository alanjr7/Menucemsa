# Backup diario de la base de datos cemsa2 (independiente de Laravel)
# Hace mysqldump, comprime a .zip con fecha y elimina backups mas viejos que $RetencionDias

# ---------- CONFIGURACION ----------
$MysqlDumpExe = "C:\xampp\mysql\bin\mysqldump.exe"
$DbHost       = "127.0.0.1"
$DbPort       = "3306"
$DbNombre     = "cemsa2"
$DbUsuario    = "root"
$DbPassword   = ""                       # vacio = sin password (XAMPP por defecto)
$DestinoDir   = "D:\Backups\cemsa2"      # CAMBIAR: idealmente otra unidad/disco
$RetencionDias = 30                       # cuantos dias de backups conservar
# -----------------------------------

$ErrorActionPreference = "Stop"
$fecha   = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
$sqlFile = Join-Path $DestinoDir "cemsa2_$fecha.sql"
$zipFile = Join-Path $DestinoDir "cemsa2_$fecha.zip"
$logFile = Join-Path $DestinoDir "backup.log"

function Log($msg) {
    $linea = "$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')  $msg"
    Add-Content -Path $logFile -Value $linea -Encoding utf8
}

try {
    if (-not (Test-Path $DestinoDir)) { New-Item -ItemType Directory -Force -Path $DestinoDir | Out-Null }

    $args = @(
        "--host=$DbHost", "--port=$DbPort", "--user=$DbUsuario",
        "--single-transaction", "--routines", "--events", "--triggers",
        "--default-character-set=utf8mb4", $DbNombre
    )
    if ($DbPassword -ne "") { $args = @("--password=$DbPassword") + $args }

    # Volcado a archivo .sql
    & $MysqlDumpExe @args | Out-File -FilePath $sqlFile -Encoding utf8
    if ($LASTEXITCODE -ne 0) { throw "mysqldump devolvio codigo $LASTEXITCODE" }
    if ((Get-Item $sqlFile).Length -lt 1024) { throw "El backup quedo vacio o demasiado pequeno" }

    # Comprimir y borrar el .sql
    Compress-Archive -Path $sqlFile -DestinationPath $zipFile -Force
    Remove-Item $sqlFile -Force

    # Retencion: borrar backups mas viejos que N dias
    Get-ChildItem -Path $DestinoDir -Filter "cemsa2_*.zip" |
        Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-$RetencionDias) } |
        Remove-Item -Force

    Log "OK  $zipFile  ($([math]::Round((Get-Item $zipFile).Length/1MB,2)) MB)"
}
catch {
    Log "ERROR  $($_.Exception.Message)"
    if (Test-Path $sqlFile) { Remove-Item $sqlFile -Force }
    exit 1
}
