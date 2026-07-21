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

# Extensão PDO MySQL (o app acessa o banco só via PDO)
RUN docker-php-ext-install pdo_mysql

# mod_rewrite: inofensivo aqui, mas deixa .htaccess de rewrite funcionar se surgir
RUN a2enmod rewrite

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
