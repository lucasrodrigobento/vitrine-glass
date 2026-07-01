# Vitrine Glass — CLAUDE.md

Produto whitelabel de site vitrine para vidraçarias.
**Referência visual:** lidervidros.com.br (legado PHP em `projects/lider-vidros/`).

---

## Stack

| Camada | Tecnologia |
|---|---|
| Framework | Laravel 13 |
| Admin/CMS | Filament 5 |
| Frontend | Bootstrap 5 + CSS custom properties |
| DB local | SQLite |
| DB produção | MySQL (Hostgator) |
| PHP | 8.3 (via Docker local) |
| Assets | Vite + npm |

---

## Como subir o sistema (Docker — desenvolvimento local)

```bash
# Subir (primeira vez ou após parado)
docker compose up -d

# Acompanhar logs de inicialização
docker compose logs -f

# Parar
docker compose down

# Recriar imagem (após mudar Dockerfile ou deps)
docker compose up -d --build

# Acessar shell dentro do container
docker compose exec app bash
```

**URLs:**
- Site: http://localhost:8080
- Admin: http://localhost:8080/admin

**Primeiro acesso:** o entrypoint roda `migrate` + `db:seed` automaticamente.
Para criar o usuário admin:
```bash
docker compose exec app php artisan make:filament-user
```

### Tenant ativo em dev

Defina no `.env`:
```
TENANT_SLUG=lider-vidros   # ou box-vidros
```

O container lê `TENANT_SLUG` do `.env` — não precisa reiniciar após mudar.

### Taproot para importar imagens

```
TAPROOT_PROJECTS_PATH=/mnt/c/taproot/projects
```

Se o caminho for diferente (outro SO ou máquina), ajuste no `.env`.

---

## Setup sem Docker (bare metal)

```bash
bash setup.sh            # setup completo
bash setup.sh --fresh    # dropa e recria o banco
bash setup.sh --no-seed  # pula importação de imagens
bash setup.sh --no-assets # pula npm
```

---

## Estrutura multi-tenant

### Resolução de tenant

O middleware `ResolveTenant` resolve o tenant pelo domínio da requisição:

1. **Produção:** lê o `Host:` header → busca `tenants.dominio` no banco
2. **Dev local (localhost/127.0.0.1):** usa `TENANT_SLUG` do `.env`
3. **Cache:** 5 minutos (`Cache::remember("tenant:{dominio}")`) — limpo automaticamente ao salvar no Filament

### Como funciona no código

```php
// Disponível em qualquer lugar após o middleware
$tenant = app('tenant');        // instância Eloquent
$t      = config('tenant');     // array com todos os campos
```

### Banco de dados

| Tabela | Descrição |
|---|---|
| `tenants` | Configuração completa (cores, SEO, contato, sobre, hero) |
| `tenant_services` | Serviços → geram páginas de galeria + menu automático |
| `tenant_slides` | Slides do carrossel da home |
| `tenant_features` | Destaques da home com imagens |
| `gallery_images` | Fotos por serviço/categoria |

---

## CMS Filament (`/admin`)

### Resources

| Resource | Onde fica | O que gerencia |
|---|---|---|
| **Empresas** | `/admin/empresas` | CRUD completo — 7 abas |
| **Slides** | `/admin/slides` | Carrossel da home por empresa |
| **Galeria de Fotos** | `/admin/gallery-images` | Fotos por serviço por empresa |

### Abas do EmpresaResource

1. **Geral** — nome, slug, domínio, ativo
2. **Cores & Identidade** — color picker primária/secundária, logo, favicon, og-image
3. **Contato** — whatsapp, email, instagram, facebook, endereço, cidades, Google Maps embed, Google Ads ID
4. **SEO** — title, description, keywords, tipo Schema.org
5. **Página Inicial** — hero título/subtítulo + repeater de destaques (features) com imagens
6. **Sobre a Empresa** — título, descrição, missão, visão, valores
7. **Serviços** — repeater CRUD (slug + título + descrição) que gera menu e páginas automaticamente

### Adicionar novo cliente

1. `/admin → Empresas → Nova Empresa`
2. Preencher todas as abas
3. Adicionar slides em `/admin → Slides`
4. Adicionar fotos em `/admin → Galeria de Fotos`
5. Apontar o DNS do cliente para o servidor
6. Deployar as configurações de vhost/subdomínio

---

## Seeder de conteúdo

```bash
php artisan db:seed --class=TenantSeeder
# ou dentro do Docker:
docker compose exec app php artisan db:seed --class=TenantSeeder
```

**O que importa:**
- Líder Vidros: 3 slides + 2 features + 7 fotos vidros + 4 fotos alumínio + 126 fotos catálogo
- Box e Vidros: 4 slides + 2 features + 7 fotos vidros + 4 fotos alumínio + 11 fotos catálogo

**Origem das imagens:**
```
TAPROOT_PROJECTS_PATH/lider-vidros/images/...
TAPROOT_PROJECTS_PATH/box-vidros/images/...
```

Imagens inexistentes são puladas com `⚠` — o seeder nunca quebra.

---

## Deploy no Hostgator (produção)

### Pré-requisitos no Hostgator

- Plano que suporte SSH (recomendado) ou acesso FTP
- PHP 8.2+ ativo no cPanel (setar em "Select PHP Version")
- Banco MySQL criado no cPanel

### Passo a passo

**1. Subir os arquivos**

Via Git (se Hostgator tem Git via SSH — preferido):
```bash
ssh usuario@seudominio.com.br
cd public_html
git clone https://github.com/Investe-Tecnologia/vitrine-glass.git .
```

Via FTP: enviar tudo exceto `vendor/`, `node_modules/`, `.git/`, `storage/app/public/`.
Fazer o build de assets localmente antes e enviar `public/build/`.

**2. Configurar .env**
```bash
cp .env.example .env
nano .env
```

Variáveis críticas para Hostgator:
```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_mysql
DB_PASSWORD=senha_mysql

TENANT_SLUG=lider-vidros   # não usado em produção (usa domínio real), mas manter
```

**3. Instalar dependências via SSH**
```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan db:seed --class=TenantSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**4. Configurar document root**

No cPanel → "Subdomains" ou "Addon Domains", apontar o document root para `public/` (não para a raiz do projeto).

Se não tiver controle do document root, criar `.htaccess` na raiz:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**5. Permissões**
```bash
chmod -R 755 storage bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache   # ou o usuário do servidor
```

### Múltiplos clientes no mesmo Hostgator

Cada cliente fica em um subdomínio ou addon domain apontando para o **mesmo código**, mas com o tenant resolvido pelo domínio:

```
lidervidros.com.br   → /public_html/vitrine-glass/public/
boxevidros.com.br    → /public_html/vitrine-glass/public/   (mesmo código)
```

O `ResolveTenant` middleware faz o resto baseado no `Host:` header.

---

## Operações comuns

```bash
# Ver logs da aplicação
docker compose logs -f
docker compose exec app tail -f storage/logs/laravel.log

# Limpar caches
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:clear
docker compose exec app php artisan view:clear

# Reimportar apenas imagens de um tenant
docker compose exec app php artisan db:seed --class=TenantSeeder --force

# Criar admin
docker compose exec app php artisan make:filament-user

# Acessar DB SQLite
docker compose exec app php artisan tinker

# Rebuild após mudar Dockerfile
docker compose up -d --build

# Zerar tudo e recomeçar (CUIDADO)
docker compose down -v   # remove volumes (apaga DB e storage)
docker compose up -d
```

---

## Convenções de código

- Cores via CSS custom properties: `var(--cor-primaria)`, `var(--cor-secundaria)`
- Todo dado de tenant vem de `config('tenant')` nas views
- Instância Eloquent disponível via `app('tenant')`
- Cache de tenant: key `tenant:{dominio}` — limpo automaticamente no Filament `afterSave()`
- Menu gerado automaticamente a partir dos `TenantService` ativos — não editar manualmente
- Imagens em `storage/app/public/` → servidas via `Storage::url($path)`

## Arquivos de configuração legados

`config/tenants/*.php` — mantidos como referência, não usados em runtime.
O middleware lê do banco de dados.
