#!/usr/bin/env bash
# Cloud Run entrypoint. APP_KEY must be a Laravel AES-256-CBC key
# (base64:… with 32 decoded bytes, or 32 raw bytes). A short random string
# fails with "Unsupported cipher or incorrect key length".

set -euo pipefail

cd /app

key_ok() {
  php -r '
    $key = getenv("APP_KEY") ?: "";
    if ($key === "") {
      exit(1);
    }
    if (str_starts_with($key, "base64:")) {
      $decoded = base64_decode(substr($key, 7), true);
      exit(($decoded !== false && strlen($decoded) === 32) ? 0 : 1);
    }
    exit(strlen($key) === 32 ? 0 : 1);
  '
}

if ! key_ok; then
  APP_KEY="$(php -r 'echo "base64:" . base64_encode(random_bytes(32));')"
  export APP_KEY
  echo "APP_KEY missing or invalid for AES-256-CBC; generated a temporary key for this container. Cookie sessions will not survive a new instance. Set APP_KEY from Secret Manager." >&2
fi

php artisan config:cache --ansi
php artisan view:cache --ansi
chown -R www-data:www-data storage bootstrap/cache

php-fpm --nodaemonize --force-stderr &
fpm_pid=$!

ready=0
for _ in $(seq 1 50); do
  if { echo >/dev/tcp/127.0.0.1/9000; } 2>/dev/null; then
    ready=1
    break
  fi
  if ! kill -0 "$fpm_pid" 2>/dev/null; then
    echo "php-fpm exited during startup" >&2
    exit 1
  fi
  sleep 0.1
done

if [[ "$ready" -ne 1 ]]; then
  echo "php-fpm did not listen on 127.0.0.1:9000" >&2
  kill "$fpm_pid" 2>/dev/null || true
  exit 1
fi

caddy run --config /etc/caddy/Caddyfile --adapter caddyfile &
caddy_pid=$!

shutdown() {
  kill -TERM "$fpm_pid" "$caddy_pid" 2>/dev/null || true
  wait "$fpm_pid" 2>/dev/null || true
  wait "$caddy_pid" 2>/dev/null || true
}
trap 'shutdown; exit 0' INT TERM

set +e
wait -n "$fpm_pid" "$caddy_pid"
status=$?
set -e
shutdown
exit "$status"
