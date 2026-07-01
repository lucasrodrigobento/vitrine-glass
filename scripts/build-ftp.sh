#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────
#  Vitrine Glass — Build do pacote FTP para Hostgator
#
#  Uso:  bash scripts/build-ftp.sh [--tenant lider-vidros]
#
#  O que faz:
#    1. Instala dependências de produção (sem dev)
#    2. Compila assets (npm run build)
#    3. Roda migrations + seed em SQLite local
#    4. Copia imagens para public/storage/ (sem precisar de symlink)
#    5. Gera .env de produção
#    6. Empacota tudo em dist/vitrine-glass-ftp.zip
#
#  Após o ZIP, basta:
#    - Extrair na raiz do domínio no Hostgator
#    - Apontar document root para public/
#    - Se usar MySQL: trocar DB_* no .env e rodar migrate + seed via SSH
# ─────────────────────────────────────────────────────────────────
set -euo pipefail

RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

info()  { echo -e "${GREEN}[✓]${NC} $1"; }
warn()  { echo -e "${YELLOW}[⚠]${NC} $1"; }
error() { echo -e "${RED}[✗]${NC} $1"; }
step()  { echo -e "\n${CYAN}${BOLD}── $1 ──${NC}"; }

trap 'error "Falha na linha $LINENO → $BASH_COMMAND"; exit 1' ERR

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_DIR="$(dirname "$SCRIPT_DIR")"
DIST_DIR="${PROJECT_DIR}/dist"
BUILD_DIR="${DIST_DIR}/build"
TAPROOT="${TAPROOT_PROJECTS_PATH:-/mnt/c/taproot/projects}"

# Flags
TENANT_SLUG="lider-vidros"
for arg in "$@"; do
  case $arg in
    --tenant=*) TENANT_SLUG="${arg#*=}" ;;
    --tenant)   shift; TENANT_SLUG="$1" ;;
  esac
done

echo -e "${BOLD}"
echo "╔══════════════════════════════════════════╗"
echo "║    Vitrine Glass — Build FTP Package     ║"
echo "╚══════════════════════════════════════════╝"
echo -e "${NC}"
echo -e "  Tenant principal: ${BOLD}${TENANT_SLUG}${NC}"
echo -e "  Saída: ${BOLD}${DIST_DIR}/vitrine-glass-ftp.zip${NC}"

# ─────────────────────────────────────────────────────────────────
step "1/7  Limpando build anterior"

rm -rf "$BUILD_DIR"
mkdir -p "$BUILD_DIR"
info "Diretório limpo: $BUILD_DIR"

# ─────────────────────────────────────────────────────────────────
step "2/7  Copiando código-fonte"

rsync -a \
  --exclude='.git' \
  --exclude='.github' \
  --exclude='node_modules' \
  --exclude='vendor' \
  --exclude='storage/app/public' \
  --exclude='public/build' \
  --exclude='public/storage' \
  --exclude='database/database.sqlite' \
  --exclude='.env' \
  --exclude='dist' \
  --exclude='*.log' \
  "${PROJECT_DIR}/" "${BUILD_DIR}/"

info "Código copiado"

# ─────────────────────────────────────────────────────────────────
step "3/7  Dependências PHP (produção)"

cd "$BUILD_DIR"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --quiet
info "vendor/ gerado (somente produção)"

# ─────────────────────────────────────────────────────────────────
step "4/7  Assets front-end"

npm ci --silent
npm run build --silent
rm -rf node_modules   # remove node_modules do pacote final
info "public/build/ gerado, node_modules removido"

# ─────────────────────────────────────────────────────────────────
step "5/7  Banco SQLite + imagens"

# .env temporário para o build
cat > .env << EOF
APP_NAME="Vitrine Glass"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://placeholder.com.br

DB_CONNECTION=sqlite

TENANT_SLUG=${TENANT_SLUG}
TAPROOT_PROJECTS_PATH=${TAPROOT}
EOF

php artisan key:generate --quiet
touch database/database.sqlite
php artisan migrate --force --quiet
info "Banco SQLite criado e migrado"

# Seed — importa imagens para storage/app/public/
if [[ -d "${TAPROOT}/lider-vidros" ]] || [[ -d "${TAPROOT}/box-vidros" ]]; then
    TAPROOT_PROJECTS_PATH="${TAPROOT}" php artisan db:seed --class=TenantSeeder --force --quiet
    info "Tenants e imagens importados para storage/"
else
    warn "Taproot não encontrado em ${TAPROOT} — imagens não importadas"
    warn "Após o upload, rode: php artisan db:seed --class=TenantSeeder --force"
fi

php artisan config:cache --quiet
php artisan route:cache  --quiet
php artisan view:cache   --quiet
info "Caches de produção gerados"

# ─────────────────────────────────────────────────────────────────
step "6/7  Preparando public/storage/ para FTP (sem symlink)"

# Em Hostgator sem acesso a symlink, criamos a pasta diretamente
# Laravel serve arquivos de storage/app/public — copiamos para public/storage/
if [[ -d "storage/app/public" ]]; then
    mkdir -p public/storage
    cp -r storage/app/public/. public/storage/
    info "Imagens copiadas para public/storage/ (sem necessidade de symlink)"
fi

# Remove o .env de build — não deve ir junto
rm -f .env

# ─────────────────────────────────────────────────────────────────
step "7/7  Empacotando ZIP"

cd "$DIST_DIR"
ZIP_FILE="${DIST_DIR}/vitrine-glass-ftp.zip"
rm -f "$ZIP_FILE"
zip -r "$ZIP_FILE" build/ --quiet
info "ZIP gerado: $ZIP_FILE"

SIZE=$(du -sh "$ZIP_FILE" | cut -f1)
info "Tamanho: ${SIZE}"

# ─────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}${BOLD}╔══════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}${BOLD}║      Pacote FTP pronto! ✅                   ║${NC}"
echo -e "${GREEN}${BOLD}╚══════════════════════════════════════════════╝${NC}"
echo ""
echo -e "  Arquivo: ${BOLD}${ZIP_FILE}${NC}"
echo ""
echo -e "  ${BOLD}Próximos passos no Hostgator:${NC}"
echo "  1. Extrair o ZIP na raiz do domínio"
echo "  2. Criar .env com as configurações de produção:"
echo "     cp .env.example .env  (editar APP_URL, DB_*, APP_KEY)"
echo ""
echo -e "  ${YELLOW}Se usar MySQL (recomendado para produção):${NC}"
echo "  - Criar banco MySQL no cPanel"
echo "  - Ajustar DB_CONNECTION=mysql + credenciais no .env"
echo "  - SSH: php artisan migrate --force && php artisan db:seed --class=TenantSeeder --force"
echo ""
echo -e "  ${YELLOW}Se usar SQLite (mais simples — OK para início):${NC}"
echo "  - O arquivo database/database.sqlite já está populado"
echo "  - Só ajustar APP_URL, APP_KEY e APP_ENV no .env"
echo "  - A pasta public/storage/ já tem as imagens (sem necessidade de storage:link)"
echo ""
echo -e "  ${BOLD}Document root no cPanel:${NC} apontar para ${BOLD}public/${NC}"
echo ""
