#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKEND="$ROOT/backend/laravel-api"
TEMP_REL="backend/laravel-api-scaffold"
TEMP="$ROOT/$TEMP_REL"

if [[ -f "$BACKEND/artisan" ]]; then
  echo "Laravel já existe em backend/laravel-api. Nada foi alterado."
  exit 0
fi

mkdir -p "$BACKEND"
rm -rf "$TEMP"

if command -v composer >/dev/null 2>&1; then
  composer create-project laravel/laravel "$TEMP"
elif command -v docker >/dev/null 2>&1; then
  docker run --rm -v "$ROOT:/app" -w /app composer:2 create-project laravel/laravel "$TEMP_REL"
else
  echo "Composer e Docker não foram encontrados." >&2
  exit 1
fi

cp -a "$TEMP/." "$BACKEND/"
rm -rf "$TEMP"

ENV_FILE="$BACKEND/.env"
[[ -f "$ENV_FILE" ]] || cp "$BACKEND/.env.example" "$ENV_FILE"

set_env() {
  local key="$1" value="$2"
  if grep -qE "^${key}=" "$ENV_FILE"; then
    sed -i.bak -E "s|^${key}=.*|${key}=${value}|" "$ENV_FILE"
  else
    printf '\n%s=%s\n' "$key" "$value" >> "$ENV_FILE"
  fi
}
set_env APP_URL "http://localhost:8080"
set_env DB_CONNECTION "pgsql"
set_env DB_HOST "postgres"
set_env DB_PORT "5432"
set_env DB_DATABASE "pulsewatch"
set_env DB_USERNAME "pulsewatch"
set_env DB_PASSWORD "pulsewatch_dev"
set_env SESSION_DRIVER "redis"
set_env CACHE_STORE "redis"
set_env QUEUE_CONNECTION "database"
set_env REDIS_HOST "redis"
set_env REDIS_PORT "6379"
set_env RABBITMQ_HOST "rabbitmq"
set_env RABBITMQ_PORT "5672"
set_env RABBITMQ_USER "pulsewatch"
set_env RABBITMQ_PASSWORD "pulsewatch_dev"
rm -f "$ENV_FILE.bak"

echo "Projeto Laravel criado em backend/laravel-api."
echo "O Dockerfile e o docker-entrypoint.sh existentes foram preservados."
