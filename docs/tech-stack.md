# Tech stack

**Status: recorded** — PHP / Laravel / Blade

This is the **PHP** line of the GOV.UK Frontend language examples. Server-rendered HTML is produced by native PHP component renderers (Blade for pages). Node is used only to install the pinned `govuk-frontend` package, compile Sass, and run shared baseline/docs checks — never to render component HTML at request time.

## Two layers

| Layer                          | Stack                                                                              | Notes                                                                                |
| ------------------------------ | ---------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------ |
| **GOV.UK Frontend (upstream)** | Node package `govuk-frontend` **6.5.1**, Nunjucks macros, official `fixtures.json` | Install / Sass / fixture freshness only                                              |
| **This service (wrapper)**     | **PHP 8.4+**, **Laravel 12**, **Blade**, Composer, Pest, Pint, Larastan            | Native `App\Govuk\Renderer` HTML; pages compose `<x-govuk.*>` / `Renderer::render()` |

## Commands

```sh
npm ci
composer install
npm run build:styles
npm start                 # Sass + php artisan serve on :8000
npm test                  # Node baseline/Sass + Pest (incl. all fixtures)
./vendor/bin/pest         # PHP tests only
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
npm run verify
```

## HTML generation and parity

- Component HTML is generated in PHP (`app/Govuk/`), tracking Frontend macros/`template.njk`.
- **Primary gate:** `Renderer::render($component, $options) === fixture html` for **every** fixture (byte-for-byte; trim only outer whitespace of renderer output).
- Symfony DomCrawler is used only to format failure diffs — never to pass tests.
- Secondary Node Nunjucks freshness checks may be added under `tests/`; they do not replace PHP parity.

## Assets and baseline

- Sass: [`styles/application.scss`](../styles/application.scss) → Frontend `@use` → [`govuk-overrides.scss`](../styles/govuk-overrides.scss) last. Output: `dist/stylesheets/application.css`. Never ship prebuilt `govuk-frontend.min.css` as the long-term source. No `!important` in service CSS.
- HTTP responses use [`baseline/`](../baseline/) (cache kinds, OWASP headers, CSP hash for the `js-enabled` snippet) via `App\Baseline\Policy` / `BaselineHeaders` middleware.
- Fingerprinted CSS/JS via `App\Support\Assets`.

## SEO

Every HTML page is hidden from search engines:

1. `<meta name="robots" content="noindex, nofollow">`
2. `X-Robots-Tag: noindex, nofollow`
3. `/robots.txt` → `Disallow: /`

## Deploy

Docker Web Service on Render free tier — see [deploying-on-render.md](deploying-on-render.md).

## Coverage

Pest / PHPUnit coverage target: **100%** functions, branches, statements for application code under `app/` (excluding `app/Models` Laravel scaffolding). CI runs `./vendor/bin/pest --coverage --min=100`.

## Version pin

| Item                    | Value                                                                                                                                     |
| ----------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| Implementation language | PHP 8.4+ / Laravel 12 / Blade                                                                                                             |
| `govuk-frontend`        | `6.5.1`                                                                                                                                   |
| Sass pipeline           | `npm run build:styles`                                                                                                                    |
| Local start             | `npm start`                                                                                                                               |
| Tests                   | `npm test` / `./vendor/bin/pest`                                                                                                          |
| Upgrade entry           | [upgrading-govuk-frontend.md](upgrading-govuk-frontend.md) — always read https://github.com/alphagov/govuk-frontend/releases/latest first |
