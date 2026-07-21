#!/bin/sh
# Entrypoint de produção (Railway).
# 1) Faz o Apache escutar na porta que o Railway injeta em $PORT
# 2) Garante a pasta de uploads (Volume) gravável
# 3) Roda as migrações idempotentes (schema + admin)
# 4) Sobe o Apache em foreground
set -e

PORT="${PORT:-8080}"

# Substitui o placeholder __PORT__ pela porta real, no vhost e no ports.conf
sed -i "s/__PORT__/${PORT}/g" \
    /etc/apache2/ports.conf \
    /etc/apache2/sites-available/000-default.conf

# A pasta de uploads costuma ser um Volume — garante existência e escrita
mkdir -p /var/www/html/assets/img/produtos
chown -R www-data:www-data /var/www/html/assets/img/produtos

# Inicialização do banco (não derruba o container se o banco ainda não subiu)
php /var/www/html/deploy/bootstrap.php || echo "[entrypoint] bootstrap falhou — seguindo mesmo assim"

exec apache2-foreground
