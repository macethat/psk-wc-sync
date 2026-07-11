# Script para guardar el historial de chat en GitHub
# Uso: .\guardar-sesion.ps1 "mensaje opcional"

param(
    [string]$mensaje = ""
)

$fecha = Get-Date -Format "yyyy-MM-dd"
$commitMsg = if ($mensaje) { "sesion $fecha - $mensaje" } else { "sesion $fecha" }

Write-Host "📝 Agregando archivos al repo..." -ForegroundColor Cyan
git add docs/historial-chat.md
git add guardar-sesion.ps1

Write-Host "📦 Haciendo commit: $commitMsg" -ForegroundColor Cyan
git commit -m $commitMsg

Write-Host "☁️ Subiendo a GitHub..." -ForegroundColor Cyan
git push origin master

Write-Host "✅ Listo. Sesion guardada: $fecha" -ForegroundColor Green
