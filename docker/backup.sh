#!/bin/sh
set -eu
umask 077
cd /home/deploy/romina-portfolio
mkdir -p backups
stamp=$(date -u +%Y%m%dT%H%M%SZ)
temporary="backups/portfolio-${stamp}.dump.tmp"
destination="backups/portfolio-${stamp}.dump"
uploads_temporary="backups/uploads-${stamp}.tar.gz.tmp"
uploads_destination="backups/uploads-${stamp}.tar.gz"
trap 'rm -f "$temporary" "$uploads_temporary"' EXIT
docker compose --env-file .env.production -f compose.production.yaml exec -T postgres \
    sh -c 'pg_dump -U "$POSTGRES_USER" -d "$POSTGRES_DB" -Fc' > "$temporary"
docker compose --env-file .env.production -f compose.production.yaml exec -T postgres \
    pg_restore --list < "$temporary" > /dev/null
docker compose --env-file .env.production -f compose.production.yaml exec -T portfolio \
    tar -C /var/www/html/storage/app/public -czf - . > "$uploads_temporary"
tar -tzf "$uploads_temporary" > /dev/null
mv "$temporary" "$destination"
mv "$uploads_temporary" "$uploads_destination"
find backups -type f -name 'portfolio-*.dump' -mtime +14 -delete
find backups -type f -name 'uploads-*.tar.gz' -mtime +14 -delete
