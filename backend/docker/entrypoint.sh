#!/bin/sh
set -eu

i=0
until php -r 'exit(gethostbyname(getenv("DB_HOST") ?: "db") === (getenv("DB_HOST") ?: "db") ? 1 : 0);'
do
  i=$((i + 1))
  if [ "$i" -gt 30 ]; then
    echo "Cannot resolve database host" >&2
    exit 1
  fi
  sleep 1
done

php bin/migrate.php
exec "$@"
