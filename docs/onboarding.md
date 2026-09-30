# Onboarding

Human-oriented map of this repository. Coding agents should treat [`AGENTS.md`](../AGENTS.md) as the dense entry point; humans should also read [`CONTRIBUTING.md`](../CONTRIBUTING.md). How docs are split for both audiences: [documentation-structure.md](documentation-structure.md).

## What this repo is

A **PHP / Laravel / Blade** example for **GDS-compliant** frontends: PHP generates HTML; **GOV.UK Frontend** is the only UI library; **no frontend frameworks** for UI. Exact **HTML parity** against official Frontend fixtures. See [project-purpose.md](project-purpose.md) and [tech-stack.md](tech-stack.md).

**GOV.UK Frontend is a Node package with Nunjucks macros upstream.** Install `govuk-frontend` from npm, treat Nunjucks `template.njk` / `fixtures.json` as the HTML contract, and keep Node scripts for Sass and shared baseline checks. **Request-time HTML is always native PHP** — never shell out to Node or Nunjucks to render components.

**Official guidance:** search the URLs in [guidance-sources.md](guidance-sources.md).

**Priorities:** frontend web performance → frontend security → reduced maintenance → accessibility → inclusive design ([priorities.md](priorities.md)).

**Documentation:** every lasting change is documented for **humans and agents** ([documentation-structure.md](documentation-structure.md)).

**HTML:** native PHP renderers in `app/Govuk/` track Frontend macros; Pest asserts byte-for-byte fixture parity. Do not copy-paste component HTML from each release as the long-term approach. Before Frontend upgrades, always read https://github.com/alphagov/govuk-frontend/releases/latest.

## Priorities

See [priorities.md](priorities.md). Short version: frontend web performance → frontend security → reduced maintenance → accessibility → inclusive design.

## Components vs patterns

| Kind          | What it is                                                               | How we build it                             | Fixture parity?                                                         |
| ------------- | ------------------------------------------------------------------------ | ------------------------------------------- | ----------------------------------------------------------------------- |
| **Component** | Design System building block (button, text input, …)                     | PHP renderer that emits exact Frontend HTML | **Yes** — official `fixtures.json`                                      |
| **Pattern**   | Guidance for a journey or page composition (addresses, check answers, …) | Compose shipped components into Blade pages | **No** — follow Design System guidance; no invented pattern HTML suites |

## Repo map

```text
AGENTS.md                 # Slim agent playbook
docs/                     # All documentation (this folder)
baseline/                 # Shared performance + OWASP header contract
styles/                   # Sass entry + govuk-overrides (compiles to dist/stylesheets/)
scripts/                  # Node build helpers (styles)
app/Govuk/                # Native PHP component renderers + parity helpers
app/Http/                 # Controllers + BaselineHeaders middleware
resources/views/          # Blade layouts and pages (compose via Renderer)
tests/Feature/Fixtures/   # Pest byte-for-byte fixture parity
tests/Feature/JourneyTest.php
Dockerfile / render.yaml  # Render free-tier Docker deploy
```

## Run modes

| Mode    | Purpose                                                                                   |
| ------- | ----------------------------------------------------------------------------------------- |
| Preview | `npm start` — Sass build + `php artisan serve` (catalogue + licence journey)              |
| Test    | `npm test` — Node baseline/Sass + Pest (fixture parity + journey)                         |
| Verify  | `npm run verify` — docs + styles + tests (CI equivalent)                                  |
| Upgrade | Mechanical Frontend bump — see [upgrading-govuk-frontend.md](upgrading-govuk-frontend.md) |

Commands: [tech-stack.md](tech-stack.md).

## Testing mindset

1. **Parity checks (primary)** compare **PHP** `Renderer::render()` output to fixture `html` with ordinal string equality — every fixture from the pinned Frontend release.
2. **Nunjucks suite (secondary)** may compare Frontend macros to stored fixture `html` to catch **stale fixtures** only.
3. **Never** edit fixture `html` to make tests pass — fix the renderer.
4. **Never** normalise HTML in tests to pass parity (DomCrawler is for failure diffs only).
5. A green Nunjucks suite alone does **not** prove PHP rendering is correct.

Details: [testing-components.md](testing-components.md).

## Troubleshooting

| Symptom                        | Likely cause                                                |
| ------------------------------ | ----------------------------------------------------------- |
| Parity fails on whitespace     | Renderer ≠ Nunjucks `template.njk` / `{%-` stripping        |
| Encoding differs (`'` vs `'`)  | Used framework HTML encoder instead of Nunjucks `escape`    |
| Attribute order differs        | Built attributes in code property order, not template order |
| Preview/fixture 404 in tests   | Test host not enabling Dev/Testing routes                   |
| Logo unreadable / wrong header | Frontend 5 header classes with Frontend 6+ CSS              |
| Editing fixtures “fixes” tests | Wrong fix — update renderer                                 |

More pitfalls: [creating-components.md](creating-components.md).

## Consistency tooling

```sh
npm ci
composer install
npm run build:styles # Sass → dist/stylesheets/application.css
npm start            # local app on :8000
npm test             # baseline + Pest (incl. all fixtures)
npm run verify:docs  # Prettier + markdownlint
npm run verify       # docs + build:styles + tests
```

See [CONTRIBUTING.md](../CONTRIBUTING.md). Dotfiles: `.editorconfig`, `.prettierrc.json`, `.markdownlint-cli2.jsonc`, `.nvmrc`, `.vscode/`, `.cursor/rules/`, `.github/`.

## Next reads

1. [tech-stack.md](tech-stack.md)
2. [example-service.md](example-service.md)
3. [testing-components.md](testing-components.md)
4. [page-shell.md](page-shell.md) / [layout-chrome.md](layout-chrome.md)
5. [deploying-on-render.md](deploying-on-render.md)
