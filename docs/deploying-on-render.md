# Deploying on Render.com

Host the Laravel example service on [Render](https://render.com) as a public demo. Use a **Web Service** with the committed **Dockerfile** (PHP is not a native Render runtime paired with Node Sass).

## What this repo includes

| File                            | Role                                                    |
| ------------------------------- | ------------------------------------------------------- |
| [`render.yaml`](../render.yaml) | Blueprint: free Docker web service, `/health`           |
| [`Dockerfile`](../Dockerfile)   | Multi-stage: Node Sass → Composer → `php artisan serve` |
| `GET /health`                   | Plain `ok` for Render health checks                     |

## Option A — Blueprint

1. Push this repo to GitHub including `render.yaml` and `Dockerfile`.
2. Open the Render Dashboard → **New +** → **Blueprint**.
3. Select the repository; confirm Render detects `render.yaml`.
4. Apply. When **Live**, open the `.onrender.com` URL.

### After deploy checklist

- [ ] `https://<service>.onrender.com/health` returns `ok`
- [ ] Start page loads with GOV.UK styling (`/`)
- [ ] Component catalogue works (`/components`) — Blueprint sets `DEMOS_ENABLED=true`
- [ ] `/robots.txt` disallows `/`
- [ ] A form POST in the licence journey retains the session

## Environment

| Variable        | Behaviour                                         |
| --------------- | ------------------------------------------------- |
| `PORT`          | Injected by Render; artisan binds `0.0.0.0:$PORT` |
| `DEMOS_ENABLED` | Blueprint sets `true`                             |
| `APP_KEY`       | Optional but recommended — see below              |
| `APP_ENV`       | `production`                                      |

### `APP_KEY`

Laravel encrypts sessions/cookies with `AES-256-CBC` (32-byte key). A valid value looks like:

```text
base64:U4Hd0Onal46OAHV0UfY+4KZ1SNUuPvRdpnqjc/N/OOo=
```

Generate one locally with `php artisan key:generate --show`, then set it in Render → **Environment** → `APP_KEY` (include the `base64:` prefix).

**Do not use Render’s “Generate” button for `APP_KEY`.** That produces a short random string (for example `AYRkrJBFPcX3J07…`) which is the wrong length and causes:

`Unsupported cipher or incorrect key length`

The Docker entrypoint ([`scripts/docker-entrypoint.sh`](../scripts/docker-entrypoint.sh)) detects an missing/invalid key and generates a temporary one for that container so `/health` and pages still work. Prefer setting a stable `APP_KEY` so encrypted cookies survive restarts.

HTTPS terminates at Render. The app trusts proxies (`X-Forwarded-Proto`).

## Free plan behaviour

The service may spin down after idle time; the first request after idle can take ~30–60s. Sessions are file-based on ephemeral disk (reset on restart).
