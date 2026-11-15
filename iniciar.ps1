param(
    [ValidateRange(1, 65535)]
    [int] $Port = 8765
)

$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    throw 'PHP nao encontrado. Instale PHP 8.4.1 ou superior e adicione-o ao PATH.'
}
if (-not (Test-Path -LiteralPath "$PSScriptRoot/vendor/autoload.php")) {
    throw 'Execute composer install na pasta do projeto antes de iniciar.'
}

Write-Host "Kriko's: http://127.0.0.1:$Port"
Write-Host 'Mantenha este terminal aberto. Use Ctrl+C para encerrar.'
& php -S "127.0.0.1:$Port" -t "$PSScriptRoot/public"
exit $LASTEXITCODE
