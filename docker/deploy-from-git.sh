#!/bin/sh
set -eu

cd /home/deploy/romina-portfolio
exec 9>/tmp/romina-portfolio-deploy.lock
flock -n 9 || exit 0

git fetch --quiet origin main
current=$(git rev-parse HEAD)
next=$(git rev-parse origin/main)
[ "$current" = "$next" ] && exit 0

git merge --ff-only origin/main
if ! docker compose --env-file .env.production -f compose.production.yaml up -d --build; then
    git reset --hard "$current"
    docker compose --env-file .env.production -f compose.production.yaml up -d --build
    exit 1
fi

if ! curl --fail --silent --show-error --max-time 30 \
    --resolve portfolio.64.23.196.133.nip.io:443:127.0.0.1 \
    https://portfolio.64.23.196.133.nip.io/up >/dev/null; then
    git reset --hard "$current"
    docker compose --env-file .env.production -f compose.production.yaml up -d --build
    exit 1
fi
