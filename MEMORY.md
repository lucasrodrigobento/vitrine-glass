# vitrine-glass — MEMORY.md

**Objetivo:** Produto whitelabel para vidraçarias — site vitrine profissional com CMS Filament, multi-tenant por domínio.

**Referência visual:** `projects/lider-vidros/` (PHP puro legado — não é base de código)

**Stack:** Laravel 11 · Filament v3 · PHP 8.4 · SQLite (local/Docker) / MySQL (prod)

---

## Como subir (desenvolvimento)

```bash
docker compose up -d          # sobe tudo (migrate + seed automático)
docker compose logs -f        # acompanhar logs
docker compose down           # parar
docker compose up -d --build  # rebuild após mudar Dockerfile
```

- Site: http://localhost:8080
- Admin: http://localhost:8080/admin
- Criar admin: `docker compose exec app php artisan make:filament-user`

Tenant ativo em dev: `TENANT_SLUG=lider-vidros` no `.env`

## Setup sem Docker

```bash
bash setup.sh   # script único com tratamento de erro
```

---

## Arquitetura Multi-Tenant

- Resolução por domínio: `ResolveTenant` middleware (DB + cache 5min)
- Config injetada: `config('tenant')` + `app('tenant')` (instância Eloquent)
- Dev local: `TENANT_SLUG` no `.env`; produção: lê domínio real

## Tenants ativos

| Slug | Empresa | Domínio |
|---|---|---|
| `lider-vidros` | Líder Vidros | lidervidros.com.br |
| `box-vidros` | Box e Vidros Goiânia | boxevidros.com.br |

## Banco de dados

| Tabela | Descrição |
|---|---|
| `tenants` | Config completa (cores, SEO, contato, sobre, hero) |
| `tenant_services` | Serviços → páginas de galeria + menu automático |
| `tenant_slides` | Slides do carrossel da home |
| `tenant_features` | Destaques da home com imagens |
| `gallery_images` | Fotos por serviço/categoria |

## Filament Admin (`/admin`)

| Resource | Abas / Função |
|---|---|
| **Empresas** | 7 abas: Geral, Cores, Contato, SEO, Home, Sobre, Serviços |
| **Slides** | Carrossel da home por empresa |
| **Galeria de Fotos** | Fotos por serviço/categoria por empresa |

## Deploy no Hostgator

1. Upload dos arquivos via FTP/SSH (exceto `vendor/`, `node_modules/`)
2. Criar banco MySQL no cPanel
3. Configurar `.env` (DB_CONNECTION=mysql, credenciais)
4. Via SSH: `composer install --no-dev` → `migrate` → `db:seed` → `storage:link`
5. Document root apontar para `public/` no cPanel

**Múltiplos clientes:** mesmo código, vários domínios/subdomínios apontando para `public/`.
O `ResolveTenant` middleware resolve pelo `Host:` header.

Documentação completa: `CLAUDE.md` (no projeto)

## Notas

- Config files `config/tenants/*.php` são referência legada — não usados em runtime
- Cache tenant: `Cache::forget("tenant:{dominio}")` — limpo automaticamente ao salvar no Filament
- Imagens em `storage/app/public/` → `php artisan storage:link` obrigatório
- Menu gerado automaticamente a partir dos serviços ativos
- `TAPROOT_PROJECTS_PATH` no `.env` define onde o seeder busca imagens source

## Como subir — regras críticas

```bash
docker compose up -d           # SEMPRE usar isso (recria container, recarrega env vars)
docker compose up -d --build   # rebuild de imagem (após mudar Dockerfile ou deps)
docker compose down -v         # reset total (apaga volumes — perde banco e storage!)
```

> **NUNCA usar `docker compose restart`** para mudanças de `.env`.  
> O `restart` mantém as variáveis de ambiente da criação original — mudanças no `.env` só são aplicadas com `docker compose up`.

## Variáveis de ambiente — precedência

O `environment:` block do docker-compose.yml **sempre sobrepõe** o `env_file:`.  
Por isso `DB_CONNECTION: sqlite` está fixo no compose — não depende do `.env` local.

| Var | Onde definida | Valor |
|---|---|---|
| `DB_CONNECTION` | `docker-compose.yml` environment block (fixo) | `sqlite` |
| `TENANT_SLUG` | `.env` → substitui `${TENANT_SLUG:-lider-vidros}` | `lider-vidros` (default) |
| `TAPROOT_PROJECTS_PATH` | docker-compose.yml (fixo) | `/taproot/projects` |

## Estado atual (2026-07-02)

- Container rodando: ✅ http://localhost:8080
- Todas as 9 migrations: ✅
- Seeder: ✅ lider-vidros + box-vidros importados do taproot
- Admin Filament: http://localhost:8080/admin (criar user: `docker compose exec app php artisan make:filament-user`)

## Sessões

| Data | O que foi feito | Score |
|---|---|---|
| 2026-07-01 | Arquitetura multi-tenant completa (Tenant, services, slides, features, gallery). Docker + entrypoint.sh. TenantSeeder importando taproot. Fix bug SQLite `->after('id')` → remove `->after()`. Primeira avaliação formal: **7.4/10** | 7.4 |
| 2026-07-02 | Fix DB_CONNECTION: `.env` tinha `mysql`, `docker compose restart` mantinha env antiga → adicionado `DB_CONNECTION: sqlite` fixo no docker-compose.yml `environment:` block. Container rodando com sucesso. | — |

## Bugs conhecidos

| # | Bug | Causa raiz | Fix aplicado |
|---|---|---|---|
| BUG-01 | Container restart loop em "Rodando migrations..." | `->after('id')` em `Schema::table()` SQLite → full table rebuild silencioso → exit 1 | Removido `->after()` da migration 5. Reset volume: `docker compose down -v && docker compose up -d --build` |
| BUG-02 | Migration tentando conectar MySQL mesmo com `.env` sqlite | `docker compose restart` não recarrega `env_file` — mantém vars da criação. Laravel lê OS env vars com precedência sobre `.env` | Adicionado `DB_CONNECTION: sqlite` fixo no `environment:` block do docker-compose.yml |

*Atualizado: 2026-07-02*
