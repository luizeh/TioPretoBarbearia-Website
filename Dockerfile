# ============================================================
# Tio Preto Barbearia — imagem de produção (Railway)
# PHP 8.2 + Apache (mod_php), espelhando o ambiente XAMPP local.
# ============================================================

# --- Estágio 1: assets de front (SweetAlert2 via npm) ---
FROM node:20-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install --omit=dev --no-audit --no-fund

# --- Estágio 2: aplicação PHP + Apache ---
FROM php:8.2-apache

# Utilitários que o Composer precisa para extrair pacotes (a imagem base não os traz)
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

# Extensão PDO MySQL (o app acessa o banco só via PDO)
RUN docker-php-ext-install pdo_mysql

# Limites de upload: a imagem base vem com upload_max_filesize=2M, pequeno
# demais para fotos de celular (3-8MB). Sobe para 10M/12M.
RUN { \
      echo 'upload_max_filesize=10M'; \
      echo 'post_max_size=12M'; \
      echo 'memory_limit=256M'; \
    } > /usr/local/etc/php/conf.d/zz-uploads.ini

# mod_rewrite: inofensivo aqui, mas deixa .htaccess de rewrite funcionar se surgir
RUN a2enmod rewrite

# Garante um ÚNICO MPM. O mod_php exige o prefork. Algumas builds da imagem
# base vêm com mpm_event E mpm_prefork habilitados ao mesmo tempo, o que
# impede o Apache de iniciar ("More than one MPM loaded"). Remover os symlinks
# na marra é à prova de falha (independe do estado da base ou do a2dismod).
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
    && a2enmod mpm_prefork \
    && ! apache2ctl -t 2>&1 | grep -q "More than one MPM"

# Composer (copiado da imagem oficial)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Dependências PHP primeiro (aproveita cache de camada do Docker)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Código da aplicação (vendor/ e node_modules/ ficam de fora via .dockerignore)
COPY . .

# node_modules gerado no estágio de assets
COPY --from=assets /app/node_modules ./node_modules

# Config do Apache: vhost com AllowOverride All + proteção da pasta de uploads
COPY deploy/apache.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/Listen 80/Listen __PORT__/' /etc/apache2/ports.conf

# Entrypoint: injeta a porta do Railway, ajusta permissões e roda as migrações
COPY deploy/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
