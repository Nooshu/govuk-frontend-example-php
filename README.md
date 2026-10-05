# GOV.UK Frontend example — PHP

> [!IMPORTANT]
> You are free to fork, modify, and maintain this repository for your own use.
>
> This includes using and adapting it within your department, organisation, or project.

> [!WARNING]
> ### 🚨 Example repository only
>
> This repository is a **demonstration only**. It will not be actively maintained or supported.
>
> **It is not an official UK government project.** It is not endorsed, maintained, or supported by any UK government department, the Government Digital Service (GDS), or the GOV.UK Design System team.
>
> **No ongoing support or updates will be provided.** This includes maintenance, dependency updates, security fixes, or technical support.
>
> **Use this code at your own risk.** You are responsible for reviewing, testing, securing, and maintaining the code, and for determining whether it is suitable for use in a service or production environment.
>
> **You are free to fork, modify, and maintain this repository for your own use.**
>
> This repository is released under the [MIT Licence](LICENSE). See the licence for the full terms.

PHP **Laravel** + **Blade** example that server-renders [GOV.UK Frontend](https://frontend.design-system.service.gov.uk/) components with **100% official fixture HTML parity**.

This is a demonstration. It is not a live government service.

## Stack

- PHP 8.4+ / Laravel 12 / Blade / Composer / Pest
- GOV.UK Frontend **6.5.1** (pinned via npm)
- Sass pipeline (`styles/` → `govuk-overrides.scss` last) — not the prebuilt dist CSS
- Shared [`baseline/`](baseline/) security and cache headers

See [`docs/tech-stack.md`](docs/tech-stack.md) and [`AGENTS.md`](AGENTS.md).

## Quick start

```sh
npm ci
composer install
cp .env.example .env   # if needed; artisan key:generate
php artisan key:generate
npm start
```

Open <http://127.0.0.1:8000>.

## What you get

1. **Component catalogue** at `/components` — every Frontend component with fixture versions and a live preview. The green “HTML matches the fixture” banner appears **only** when PHP output equals the official fixture HTML.
2. **Fishing rod licence journey** from Start now through confirmation — GDS form patterns (`novalidate`, error summary, retained values).

## Tests

```sh
npm test
```

Runs Node baseline/Sass coverage gates and Pest, including byte-for-byte fixture parity for every shipped fixture.

## Deploy

Docker on Render free tier — [`docs/deploying-on-render.md`](docs/deploying-on-render.md).

## Licence and security

- Code in this repository: [MIT License](LICENSE)
- How to report vulnerabilities: [SECURITY.md](SECURITY.md)
- GOV.UK Design System and Frontend are maintained by GDS; Crown copyright / OGL apply to GOV.UK content patterns as documented on GOV.UK.
