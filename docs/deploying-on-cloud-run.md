# Deploying on Google Cloud Run

Host the Laravel example service on [Cloud Run](https://cloud.google.com/run/docs). The container listens on `$PORT` (Cloud Run’s default is 8080). Caddy terminates HTTP inside the container and proxies to PHP-FPM. TLS stops at Cloud Run, which sets `X-Forwarded-Proto`. The app trusts proxies.

You need a Google Cloud project with billing enabled, the [gcloud CLI](https://cloud.google.com/sdk/docs/install), and `openssl`. Cloud Run can scale to zero. Build minutes, Artifact Registry storage, and requests above the free tier are billed.

## What this repo includes

| File                                                            | Role                                                                |
| --------------------------------------------------------------- | ------------------------------------------------------------------- |
| [`Dockerfile`](../Dockerfile)                                   | Node Sass, Composer, then Caddy + PHP-FPM                           |
| [`docker/Caddyfile`](../docker/Caddyfile)                       | Brotli, then Gzip; strips `Server`                                  |
| [`docker/entrypoint.sh`](../docker/entrypoint.sh)               | Checks `APP_KEY`, caches config and views, starts PHP-FPM and Caddy |
| [`scripts/deploy-cloud-run.sh`](../scripts/deploy-cloud-run.sh) | Enables APIs, stores `APP_KEY`, deploys from source                 |
| `GET /health`                                                   | Plain `ok` for startup, readiness, and liveness probes              |

`/robots.txt` is the Laravel route and answers `Disallow: /`. Caddy serves real files under `public/` itself, so that route stays the only robots response.

## Deploy

From the repo root, with a project selected:

```sh
gcloud config set project PROJECT_ID
./scripts/deploy-cloud-run.sh
```

The script defaults to region `europe-west2` (London) and service name `govuk-frontend-example-php`. Override with environment variables:

| Variable        | Default                                              |
| --------------- | ---------------------------------------------------- |
| `PROJECT`       | gcloud’s active project                              |
| `REGION`        | `europe-west2`                                       |
| `SERVICE`       | `govuk-frontend-example-php`                         |
| `SECRET`        | `govuk-frontend-example-php-app-key`                 |
| `MEMORY`        | `1Gi`                                                |
| `CPU`           | `1`                                                  |
| `MIN_INSTANCES` | `0`                                                  |
| `MAX_INSTANCES` | `3`                                                  |
| `RUNTIME_SA`    | the project’s Compute Engine default service account |

`CONCURRENCY` stays `8`. That matches `pm.max_children` in [`docker/php-fpm.conf`](../docker/php-fpm.conf). Raise both together if you change them, and raise `MEMORY` if workers start to run out of RAM.

The first run creates a Secret Manager secret with a real Laravel key (`base64:` and 32 decoded bytes). Later deploys reuse it, so encrypted cookies survive new revisions. Do not replace that secret with a short random string. Cloud Run’s own secret generator is the wrong length and causes `Unsupported cipher or incorrect key length`.

To rotate the key:

```sh
php artisan key:generate --show | gcloud secrets versions add govuk-frontend-example-php-app-key --data-file=-
./scripts/deploy-cloud-run.sh
```

Existing session cookies stop working after a rotation. That is expected.

### After deploy checklist

- [ ] `https://<service>-<hash>.<region>.run.app/health` returns `ok`
- [ ] Start page loads with GOV.UK styling (`/`)
- [ ] Component catalogue works (`/components`) — the script sets `DEMOS_ENABLED=true`
- [ ] `/robots.txt` disallows `/`
- [ ] A form POST in the licence journey keeps the answers on the next page
- [ ] A response that sends `Accept-Encoding: br` is Brotli (`Content-Encoding: br`)
- [ ] `Server` and `X-Powered-By` are absent

Set `APP_URL` to the service URL if you generate absolute links outside a request:

```sh
gcloud run services update govuk-frontend-example-php \
  --region europe-west2 \
  --update-env-vars APP_URL=https://SERVICE_URL
```

## Environment

| Variable                | Behaviour                                                     |
| ----------------------- | ------------------------------------------------------------- |
| `PORT`                  | Injected by Cloud Run. Caddy binds `:${PORT}`                 |
| `DEMOS_ENABLED`         | Script sets `true` so the catalogue stays on in production    |
| `APP_KEY`               | Secret Manager. Required for a stable session cookie          |
| `APP_ENV`               | `production`                                                  |
| `APP_DEBUG`             | `false`                                                       |
| `SESSION_DRIVER`        | `cookie`, so the journey survives scale-out and scale-to-zero |
| `SESSION_SECURE_COOKIE` | `true`, because Cloud Run is HTTPS                            |
| `LOG_CHANNEL`           | `stderr` (Cloud Logging)                                      |

File sessions are local to one instance and disappear when that instance stops. The image uses the cookie driver instead. The licence journey payload is small enough for a cookie.

If `APP_KEY` is missing or the wrong length, the entrypoint generates a temporary key so `/health` still returns `ok`. Cookie sessions from that instance will not decrypt anywhere else. Prefer the secret.

## Request path

Cloud Run sends HTTP/1 to the container. Caddy listens on `0.0.0.0:$PORT`, compresses, and forwards to PHP-FPM on `127.0.0.1:9000`. Laravel sends the baseline headers. The stock Caddy binary has no Brotli encoder, so the image builds Caddy 2.11.4 with `github.com/ueffel/caddy-brotli` v1.7.0. Caddy chooses the encoding:

1. Brotli when `Accept-Encoding` includes `br`
2. Gzip when the client does not advertise `br`
3. No `Content-Encoding` otherwise

Caddy keeps `ETag` and suffixes it with the encoding (`-br` or `-gzip`) so each representation has its own validator. `Cache-Control` and `Vary` are unchanged. PHP does not set `Content-Encoding`.

`expose_php` is off. The Caddyfile deletes `Server` and `Via` after the response is written, because Caddy would otherwise add them behind Laravel.

## Run the image locally

```sh
docker build -t govuk-frontend-example-php .
docker run --rm -p 8080:8080 -e SESSION_SECURE_COOKIE=false govuk-frontend-example-php
```

Open <http://127.0.0.1:8080>. `SESSION_SECURE_COOKIE=false` lets the session cookie travel over local HTTP. The Cloud Run script sets it back to `true`.

`APP_KEY` can be omitted for a local container. The entrypoint will invent one until the container stops.

## Troubleshooting

| What you see                                                | What to do                                                                                                                          |
| ----------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------- |
| Cloud Build cannot update Cloud Run                         | The account that runs the script needs Cloud Run Admin, Cloud Build Editor, and Service Account User on the runtime service account |
| `/health` returns 500 and the log says incorrect key length | The secret is not a Laravel key. Add a version from `php artisan key:generate --show` and deploy again                              |
| The journey forgets answers                                 | `APP_KEY` changed between instances, or `SESSION_DRIVER` is `file`. Keep the cookie driver and one secret                           |
| First request is slow                                       | `MIN_INSTANCES` is `0`, so Cloud Run starts an instance on demand. Set `MIN_INSTANCES=1` to keep one warm                           |
