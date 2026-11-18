# Bootstrap do Laravel - PulseWatch

Este pacote prepara a criação do projeto Laravel dentro de `backend/laravel-api`,
sem sobrescrever o Dockerfile e o docker-entrypoint.sh que já fazem parte da
infraestrutura.

## Windows (PowerShell)

Na raiz do repositório PulseWatch:

```powershell
Copy-Item .env.example .env
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\scripts\bootstrap-laravel.ps1
```

O script usa Composer instalado na máquina. Se não encontrar Composer, tenta
usar a imagem `composer:2` pelo Docker.

## Linux / macOS

Na raiz do repositório:

```bash
cp .env.example .env
chmod +x scripts/bootstrap-laravel.sh
./scripts/bootstrap-laravel.sh
```

## Depois da criação

Confira se estes arquivos existem:

```text
backend/laravel-api/artisan
backend/laravel-api/composer.json
backend/laravel-api/.env
backend/laravel-api/Dockerfile
backend/laravel-api/docker-entrypoint.sh
```

O script configura o `.env` do Laravel para acessar os serviços pelos nomes
da rede Docker: `postgres`, `redis` e `rabbitmq`.

As credenciais fornecidas são apenas para desenvolvimento local. Não use esses
valores em produção.

## Próxima execução

Depois de criar o Laravel, na raiz do repositório:

```bash
docker compose config
docker compose up -d --build postgres redis rabbitmq laravel-api
docker compose exec laravel-api php artisan key:generate
docker compose exec laravel-api php artisan migrate
```

A execução das migrations depende de elas estarem presentes no projeto Laravel.
Se as migrations PulseWatch ainda não tiverem sido copiadas para
`backend/laravel-api/database/migrations`, faça isso antes do comando `migrate`.

Observação: o scaffold Laravel é baixado pelo Composer no momento da execução
do script. Este pacote não contém uma cópia do framework.
