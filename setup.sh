#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────
#  vitrine-glass — Setup completo
#  Uso:  bash setup.sh [--fresh] [--no-seed] [--no-assets]
#
#  --fresh      dropa e recria o banco (cuidado em produção)
#  --no-seed    pula o seeder (não importa imagens/tenants)
#  --no-assets  pula npm install + build
# ─────────────────────────────────────────────────────────────────
set -euo pipefail

# ── Cores ────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'
BLUE='\033[0;34m'; CYAN='\033[0;36m'; BOLD='\033[1m'; NC='\033[0m'

# ── Flags ────────────────────────────────────────────────────────
FRESH=false
NO_SEED=false
NO_ASSETS=false
for arg in "$@"; do
  case $arg in
    --fresh)     FRESH=true      ;;
    --no-seed)   NO_SEED=true    ;;
    --no-assets) NO_ASSETS=true  ;;
  esac
done

# ── Helpers ──────────────────────────────────────────────────────
info()    { echo -e "${GREEN}[✓]${NC} $1"; }
warn()    { echo -e "${YELLOW}[⚠]${NC} $1"; }
error()   { echo -e "${RED}[✗]${NC} $1"; }
step()    { echo -e "\n${CYAN}${BOLD}── $1 ──${NC}"; }
banner()  { echo -e "${BLUE}${BOLD}$1${NC}"; }
abort()   { error "$1"; exit 1; }

# ── Trap de erro ─────────────────────────────────────────────────
trap 'error "Falha na linha $LINENO  →  $BASH_COMMAND"; exit 1' ERR

# ── Diretório do script ──────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# ─────────────────────────────────────────────────────────────────
banner "
╔══════════════════════════════════════════╗
║      Vitrine Glass — Setup Completo      ║
╚══════════════════════════════════════════╝"
echo -e "  Diretório: ${BOLD}${SCRIPT_DIR}${NC}"
echo -e "  Flags: fresh=${BOLD}${FRESH}${NC}  no-seed=${BOLD}${NO_SEED}${NC}  no-assets=${BOLD}${NO_ASSETS}${NC}"

# ─────────────────────────────────────────────────────────────────
step "1/8  Verificando dependências"

check_cmd() {
  if ! command -v "$1" &>/dev/null; then
    abort "$1 não encontrado. Instale antes de continuar."
  fi
  info "$1 encontrado: $(command -v "$1")"
}

check_cmd php
check_cmd composer

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
REQUIRED="8.3"
if [[ "$(printf '%s\n' "$REQUIRED" "$PHP_VERSION" | sort -V | head -n1)" != "$REQUIRED" ]]; then
  abort "PHP >= 8.3 necessário. Versão atual: $PHP_VERSION"
fi
info "PHP $PHP_VERSION OK"

if ! $NO_ASSETS; then
  check_cmd npm
fi

# ─────────────────────────────────────────────────────────────────
step "2/8  Configurando .env"

if [[ ! -f ".env" ]]; then
  if [[ -f ".env.example" ]]; then
    cp .env.example .env
    info ".env criado a partir de .env.example"
  else
    abort ".env.example não encontrado. Crie um .env manualmente."
  fi
else
  info ".env já existe — mantendo"
fi

# Garantir APP_KEY
if grep -q "^APP_KEY=$" .env || ! grep -q "^APP_KEY=" .env; then
  php artisan key:generate --ansi
  info "APP_KEY gerado"
else
  info "APP_KEY já definida"
fi

# Detectar e configurar DB SQLite automaticamente se necessário
if grep -q "^DB_CONNECTION=sqlite" .env || ! grep -q "^DB_CONNECTION=" .env; then
  DB_FILE="${SCRIPT_DIR}/database/database.sqlite"
  if [[ ! -f "$DB_FILE" ]]; then
    touch "$DB_FILE"
    info "SQLite criado: $DB_FILE"
  else
    info "SQLite já existe"
  fi
fi

# ─────────────────────────────────────────────────────────────────
step "3/8  Instalando dependências PHP"

if [[ ! -d "vendor" ]]; then
  composer install --no-interaction --prefer-dist --optimize-autoloader
  info "Composer: dependências instaladas"
else
  info "vendor/ já existe — pulando (use 'composer install' manualmente se precisar atualizar)"
fi

# ─────────────────────────────────────────────────────────────────
step "4/8  Banco de dados"

if $FRESH; then
  warn "Modo --fresh: banco será resetado!"
  read -r -p "  Confirma? (s/N) " CONFIRM
  if [[ "${CONFIRM,,}" != "s" ]]; then
    abort "Cancelado pelo usuário."
  fi
  php artisan migrate:fresh --force --ansi
  info "Banco recriado do zero"
else
  php artisan migrate --force --ansi
  info "Migrations executadas"
fi

# ─────────────────────────────────────────────────────────────────
step "5/8  Storage link"

if [[ -L "public/storage" ]]; then
  info "Symlink public/storage já existe"
else
  php artisan storage:link --ansi
  info "Symlink criado: public/storage → storage/app/public"
fi

# ─────────────────────────────────────────────────────────────────
step "6/8  Importando tenants e conteúdo"

if $NO_SEED; then
  warn "Pulando seeder (--no-seed)"
else
  # Caminho padrão do taproot para encontrar os projetos fonte
  TAPROOT_PATH="${TAPROOT_PROJECTS_PATH:-/mnt/c/taproot/projects}"

  LIDER_SRC="${TAPROOT_PATH}/lider-vidros"
  BOX_SRC="${TAPROOT_PATH}/box-vidros"

  if [[ ! -d "$LIDER_SRC" ]]; then
    warn "Projeto lider-vidros não encontrado em: $LIDER_SRC"
    warn "Defina TAPROOT_PROJECTS_PATH no .env ou exporte antes de rodar o script."
    warn "As imagens não serão importadas, mas os dados textuais serão."
  fi

  if [[ ! -d "$BOX_SRC" ]]; then
    warn "Projeto box-vidros não encontrado em: $BOX_SRC"
  fi

  # Passa o caminho via env para o seeder
  TAPROOT_PROJECTS_PATH="$TAPROOT_PATH" php artisan db:seed --class=TenantSeeder --force --ansi
  info "Tenants e conteúdo importados"
fi

# ─────────────────────────────────────────────────────────────────
step "7/8  Assets front-end"

if $NO_ASSETS; then
  warn "Pulando build de assets (--no-assets)"
else
  if [[ ! -d "node_modules" ]]; then
    npm install --silent
    info "npm: dependências instaladas"
  else
    info "node_modules já existe"
  fi

  npm run build
  info "Assets compilados"
fi

# ─────────────────────────────────────────────────────────────────
step "8/8  Usuário admin Filament"

# Só cria se não existir nenhum usuário
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1 || echo "0")

if [[ "$USER_COUNT" == "0" ]]; then
  echo ""
  warn "Nenhum usuário admin encontrado."
  read -r -p "  Criar usuário admin agora? (s/N) " CREATE_USER
  if [[ "${CREATE_USER,,}" == "s" ]]; then
    php artisan make:filament-user
  else
    warn "Lembre de criar um admin depois: php artisan make:filament-user"
  fi
else
  info "Usuário(s) existente(s): $USER_COUNT  — pulando criação"
fi

# ─────────────────────────────────────────────────────────────────
echo ""
banner "╔══════════════════════════════════════════╗"
banner "║           Setup concluído! ✅             ║"
banner "╚══════════════════════════════════════════╝"
echo ""
echo -e "  ${BOLD}Próximos passos:${NC}"
echo -e "  1. Ajuste o ${BOLD}.env${NC} (mail, DB de produção, etc.)"
echo -e "  2. Acesse o admin: ${CYAN}http://localhost:8000/admin${NC}"
echo -e "  3. Para dev: ${BOLD}composer dev${NC}"
echo -e "  4. Para adicionar novo cliente: acesse ${CYAN}/admin → Empresas → Nova Empresa${NC}"
echo ""
echo -e "  ${YELLOW}Tenants ativos:${NC}"
echo -e "  • lider-vidros  → lidervidros.com.br"
echo -e "  • box-vidros    → boxevidros.com.br"
echo -e "  (dev local: defina ${BOLD}TENANT_SLUG=lider-vidros${NC} no .env)"
echo ""
