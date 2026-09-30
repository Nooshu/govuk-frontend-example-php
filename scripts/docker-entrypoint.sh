#!/bin/sh
# Ensure APP_KEY is a valid Laravel AES-256-CBC key before serving.
# Render's generateValue secrets are the wrong length and cause:
# "Unsupported cipher or incorrect key length"

set -eu

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
  echo "APP_KEY missing or invalid for AES-256-CBC; generated a temporary key for this container." >&2
fi

exec php -d variables_order=EGPCS artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
