$ErrorActionPreference = "Stop"

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot "..")).Path
$backendDir = Join-Path $projectRoot "backend/laravel-api"
$tempRelative = "backend/laravel-api-scaffold"
$tempDir = Join-Path $projectRoot $tempRelative

if (Test-Path (Join-Path $backendDir "artisan")) {
    Write-Host "Laravel já existe em backend/laravel-api. Nada foi alterado."
    exit 0
}

if (Test-Path $tempDir) {
    Remove-Item $tempDir -Recurse -Force
}

New-Item -ItemType Directory -Force -Path $backendDir | Out-Null

$composer = Get-Command composer -ErrorAction SilentlyContinue
if ($composer) {
    Write-Host "Criando projeto Laravel com Composer local..."
    composer create-project laravel/laravel $tempRelative
    if ($LASTEXITCODE -ne 0) { throw "composer create-project falhou." }
} else {
    $docker = Get-Command docker -ErrorAction SilentlyContinue
    if (-not $docker) {
        throw "Composer e Docker não foram encontrados. Instale um deles e execute novamente."
    }
    Write-Host "Composer local não encontrado. Usando a imagem oficial composer:2..."
    $mountRoot = $projectRoot.Replace("\", "/")
    docker run --rm -v "${mountRoot}:/app" -w /app composer:2 create-project laravel/laravel $tempRelative
    if ($LASTEXITCODE -ne 0) { throw "Criação do Laravel pelo Docker falhou." }
}

Get-ChildItem -Force $tempDir | ForEach-Object {
    Copy-Item -Path $_.FullName -Destination $backendDir -Recurse -Force
}
Remove-Item $tempDir -Recurse -Force

$envFile = Join-Path $backendDir ".env"
if (-not (Test-Path $envFile)) {
    Copy-Item (Join-Path $backendDir ".env.example") $envFile
}

$content = Get-Content $envFile -Raw
$replacements = @{
    'APP_URL=.*' = 'APP_URL=http://localhost:8080'
    'DB_CONNECTION=.*' = 'DB_CONNECTION=pgsql'
    'DB_HOST=.*' = 'DB_HOST=postgres'
    'DB_PORT=.*' = 'DB_PORT=5432'
    'DB_DATABASE=.*' = 'DB_DATABASE=pulsewatch'
    'DB_USERNAME=.*' = 'DB_USERNAME=pulsewatch'
    'DB_PASSWORD=.*' = 'DB_PASSWORD=pulsewatch_dev'
    'SESSION_DRIVER=.*' = 'SESSION_DRIVER=redis'
    'CACHE_STORE=.*' = 'CACHE_STORE=redis'
    'QUEUE_CONNECTION=.*' = 'QUEUE_CONNECTION=database'
    'REDIS_HOST=.*' = 'REDIS_HOST=redis'
    'REDIS_PORT=.*' = 'REDIS_PORT=6379'
}
foreach ($pattern in $replacements.Keys) {
    if ($content -match "(?m)^$pattern$") {
        $content = [regex]::Replace($content, "(?m)^$pattern$", $replacements[$pattern])
    } else {
        $content += "`n$($replacements[$pattern])"
    }
}
$content += "`nRABBITMQ_HOST=rabbitmq`nRABBITMQ_PORT=5672`nRABBITMQ_USER=pulsewatch`nRABBITMQ_PASSWORD=pulsewatch_dev`n"
Set-Content -Path $envFile -Value $content -Encoding utf8

Write-Host ""
Write-Host "Projeto Laravel criado em backend/laravel-api."
Write-Host "O Dockerfile e o docker-entrypoint.sh existentes foram preservados."
Write-Host "Próximo passo: configurar o .env da raiz e subir os serviços de infraestrutura."
