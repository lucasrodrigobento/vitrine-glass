#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────
#  Vitrine Glass — Container entrypoint
#  Roda uma vez ao iniciar o container. Idempotente.
# ─────────────────────────────────────────────────────────────────
set -euo pipefail

GREEN='\033[0;32m'; YELLOW='\033[1;33m'; RED='\033[0;31m'; NC='\033[0m'
info()  { echo -e "${GREEN}[vitrine]${NC} $1"; }
warn()  { echo -e "${YELLOW}[vitrine]${NC} $1"; }
error() { echo -e "${RED}[vitrine]${NC} $1"; }

cd /app

info "Iniciando Vitrine Glass..."

# ── 0. Dependências (vendor pode estar vazio por volume override) ─
if [[ ! -f "vendor/autoload.php" ]]; then
    info "Instalando dependências PHP (primeira vez)..."
    composer install --no-interaction --prefer-dist --optimize-autoloader --quiet
    info "Composer OK"
fi

if [[ ! -f "public/build/manifest.json" ]]; then
    info "Compilando assets (primeira vez)..."
    npm install --silent && npm run build --silent
    info "Assets OK"
fi

# ── 1. .env ──────────────────────────────────────────────────────
if [[ ! -f ".env" ]]; then
    if [[ -f ".env.example" ]]; then
        cp .env.example .env
        info ".env criado a partir de .env.example"
    else
        error ".env.example ausente — crie um .env"
        exit 1
    fi
fi

# ── 2. APP_KEY ────────────────────────────────────────────────────
if grep -qE "^APP_KEY=$|^APP_KEY=base64:$" .env; then
    php artisan key:generate --ansi --quiet
    info "APP_KEY gerado"
fi

# ── 3. SQLite ─────────────────────────────────────────────────────
# Banco fica em storage/db/ (volume isolado) para não sobrepor database/migrations/
SQLITE_DIR="storage/db"
SQLITE_FILE="${SQLITE_DIR}/database.sqlite"
mkdir -p "$SQLITE_DIR"
if [[ ! -f "$SQLITE_FILE" ]]; then
    touch "$SQLITE_FILE"
    info "SQLite criado: $SQLITE_FILE"
fi

# ── 4. Migrations ────────────────────────────────────────────────
info "Rodando migrations..."
php artisan migrate --force
info "Migrations OK"

# ── 5. Storage link ───────────────────────────────────────────────
if [[ ! -L "public/storage" ]]; then
    php artisan storage:link --quiet
    info "Symlink storage criado"
fi

# ── 6. Seeder (só se banco vazio) ────────────────────────────────
# Verifica diretamente no SQLite ou via PHP inline
TENANT_COUNT=$(php -r "
    try {
        \$pdo = new PDO('sqlite:' . realpath('storage/db/database.sqlite'));
        echo \$pdo->query(\"SELECT COUNT(*) FROM tenants\")->fetchColumn();
    } catch (Exception \$e) { echo 0; }
" 2>/dev/null || echo "0")
if [[ "$TENANT_COUNT" == "0" ]]; then
    info "Banco vazio — executando seeder..."

    TAPROOT="${TAPROOT_PROJECTS_PATH:-/mnt/c/taproot/projects}"
    if [[ -d "$TAPROOT/lider-vidros" ]] || [[ -d "$TAPROOT/box-vidros" ]]; then
        php artisan db:seed --class=TenantSeeder --force --quiet
        info "Tenants e imagens importados"
    else
        warn "Projetos fonte não encontrados em $TAPROOT — seeder pulado"
        warn "Monte /mnt/c/taproot ou defina TAPROOT_PROJECTS_PATH para importar imagens"
    fi
else
    info "Banco já populado ($TENANT_COUNT tenant(s)) — seeder ignorado"
fi

# ── 7. Cache de configuração (produção) ──────────────────────────
if [[ "${APP_ENV:-local}" == "production" ]]; then
    php artisan config:cache  --quiet
    php artisan route:cache   --quiet
    php artisan view:cache    --quiet
    info "Caches de produção gerados"
fi

# ── 8. Servidor ───────────────────────────────────────────────────
info "Servidor rodando em http://0.0.0.0:8080"
info "Admin: http://localhost:8080/admin"
echo ""

exec php artisan serve --host=0.0.0.0 --port=8080
