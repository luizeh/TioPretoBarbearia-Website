#!/bin/sh
# Entrypoint de produção (Railway).
# 1) Faz o Apache escutar na porta que o Railway injeta em $PORT
# 2) Garante a pasta de uploads (Volume) gravável
# 3) Roda as migrações idempotentes (schema + admin)
# 4) Sobe o Apache em foreground
set -e

PORT="${PORT:-8080}"

# Garante um ÚNICO MPM em TEMPO DE EXECUÇÃO. A imagem base às vezes vem com
# mpm_event + mpm_prefork habilitados, e o cache de build do Railway pode
# preservar isso apesar da limpeza no Dockerfile. Remover aqui, a cada start,
# é imune a cache: roda sobre o filesystem real antes do Apache iniciar.
# O mod_php exige o prefork.
rm -f /etc/apache2/mods-enabled/mpm_event.load \
      /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_worker.load \
      /etc/apache2/mods-enabled/mpm_worker.conf

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
