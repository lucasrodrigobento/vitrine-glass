# vitrine-glass — MEMORY.md

**Objetivo:** Produto whitelabel para vidraçarias — site vitrine profissional com CMS Filament, multi-tenant por domínio.

**Referência visual:** `projects/lider-vidros/` (PHP puro legado — não é base de código)

**Stack:** Laravel 13 · Filament 5 · PHP 8.3 · Bootstrap 5 · SQLite (local) / MySQL (prod)

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

*Atualizado: 2026-07-01*
