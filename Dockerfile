# ─────────────────────────────────────────────────────────────────
#  Vitrine Glass — Imagem Docker
#  PHP 8.4 + Composer + Node 20 + extensões Laravel
# ─────────────────────────────────────────────────────────────────
FROM php:8.4-cli-bookworm

LABEL maintainer="Investe Tecnologia <ti@investetecnologia.com.br>"

# ── Extensões do sistema ──────────────────────────────────────────
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip \
    libsqlite3-dev \
    libpng-dev libjpeg62-turbo-dev libwebp-dev \
    libonig-dev libzip-dev \
    libicu-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo pdo_sqlite pdo_mysql \
        mbstring zip gd bcmath intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── Composer ─────────────────────────────────────────────────────
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# ── Node.js 20 ───────────────────────────────────────────────────
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ── php.ini de desenvolvimento ───────────────────────────────────
RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
    && echo "upload_max_filesize = 20M"  >> "$PHP_INI_DIR/conf.d/vitrine.ini" \
    && echo "post_max_size = 25M"         >> "$PHP_INI_DIR/conf.d/vitrine.ini" \
    && echo "memory_limit = 256M"         >> "$PHP_INI_DIR/conf.d/vitrine.ini" \
    && echo "max_execution_time = 120"    >> "$PHP_INI_DIR/conf.d/vitrine.ini"

WORKDIR /app

# ── Copia e instala dependências (camadas separadas para cache) ───
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY package.json package-lock.json* ./
RUN npm install --silent

# ── Copia o código ────────────────────────────────────────────────
COPY . .

# ── Finaliza Composer + build assets ─────────────────────────────
RUN composer dump-autoload --optimize \
    && npm run build

# ── Entrypoint ───────────────────────────────────────────────────
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]
