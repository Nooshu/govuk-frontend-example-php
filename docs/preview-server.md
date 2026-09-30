# Preview server

Local server for human parity checks and the rod fishing licence journey.

## Start

```sh
npm ci
composer install
cp .env.example .env   # first time
php artisan key:generate
npm start
```

`npm start` builds Sass then runs `php artisan serve` on `http://127.0.0.1:8000` (`PORT` overrides the port).

See [tech-stack.md](tech-stack.md) and [example-service.md](example-service.md).

## Expectations

- `/` — start page for the licence journey; link to the component catalogue when demos are enabled.
- `/components` — catalogue of **links only** — no embedded live demos on the index.
- `/components/{name}` — selected fixture, gated parity banner, dotted preview frame, Versions list.
- `/components/{name}/fixture` — raw HTML **fragment** for automation.
- Preview responses use the same [`baseline/`](../baseline/) headers as production. On local HTTP, HSTS is not sent.
- `GET /health` → plain `ok`.
- Every HTML page: meta robots + `X-Robots-Tag` + `/robots.txt` `Disallow: /`.

## After code changes

Rebuild styles with `npm run build:styles` (or restart via `npm start`). Hard-refresh the browser. Confirm focus states, header/footer, and a failing-form example during visual QA after Frontend upgrades ([upgrading-govuk-frontend.md](upgrading-govuk-frontend.md)).
