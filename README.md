# GOV.UK Frontend example — PHP

> [!IMPORTANT]
> You are free to fork this repository and use it for your own purposes, and to modify and maintain it as you see fit.

> [!WARNING]
> 🚨 **Example repository only**
>
> This repository was created as a demonstration and will not be actively maintained or supported. It is not an official UK government project and is not endorsed, maintained, or supported by any UK government department, the Government Digital Service (GDS), or the GOV.UK Design System team.
>
> I will not be providing ongoing maintenance, updates, security fixes, or technical support.
>
> Use this code at your own risk. You are responsible for reviewing, testing, securing, maintaining, and ensuring the suitability of the code before using it in any service or production environment. I accept no responsibility or liability for any loss, damage, security issue, service failure, or other consequence resulting from its use.
>
> This repository is released under the MIT Licence. See the [LICENSE](LICENSE) file for the full licence terms.

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
