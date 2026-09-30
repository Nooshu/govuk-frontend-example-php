<!-- ============================================================
  GOV.UK Design System — Agent instructions (PHP / Laravel)
  ============================================================
  Detail lives in /docs and .cursor/skills/gds-compliant-frontend
  ============================================================ -->

♛ GOV.UK

# GOV.UK Frontend example — PHP

**PHP / Laravel / Blade** example for **GDS-compliant** government frontends: native PHP generates HTML; **[GOV.UK Frontend](https://frontend.design-system.service.gov.uk/)** (pinned **6.5.1**) is the **only** UI component library. **No frontend frameworks** (React, Vue, Angular, Svelte, etc.) for UI.

Component HTML is produced by `App\Govuk\Renderer` tracking Frontend macros / `template.njk`. **Never** Nunjucks or Node at request time. Official **test fixtures** are the contract: PHP HTML must match every fixture `html` byte-for-byte.

**LIVE guidance** — [Design System feedback](https://design-system.service.gov.uk/community/feedback/).

Skill: [`.cursor/skills/gds-compliant-frontend/SKILL.md`](.cursor/skills/gds-compliant-frontend/SKILL.md). Purpose: [`docs/project-purpose.md`](docs/project-purpose.md). Stack: [`docs/tech-stack.md`](docs/tech-stack.md).

## Priorities (in order)

1. Frontend web performance
2. Frontend security
3. Reduced maintenance
4. Accessibility
5. Inclusive design

Details: [`docs/priorities.md`](docs/priorities.md).

## Start here

| Audience                  | Doc                                                                                                                     |
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| **Human developers**      | [`docs/onboarding.md`](docs/onboarding.md), [`CONTRIBUTING.md`](CONTRIBUTING.md)                                        |
| **AI agents (this file)** | Keep reading; skill: [`.cursor/skills/gds-compliant-frontend/SKILL.md`](.cursor/skills/gds-compliant-frontend/SKILL.md) |
| Dual-audience docs map    | [`docs/documentation-structure.md`](docs/documentation-structure.md), [`docs/README.md`](docs/README.md)                |
| Project purpose           | [`docs/project-purpose.md`](docs/project-purpose.md)                                                                    |
| Official guidance URLs    | [`docs/guidance-sources.md`](docs/guidance-sources.md)                                                                  |
| Stack                     | [`docs/tech-stack.md`](docs/tech-stack.md) — PHP 8.4+ / Laravel 12 / Blade                                              |

**HTML generation:** Native PHP in `app/Govuk/` — do not require Node at request time. Track Frontend macros/`template.njk` and prove PHP ≡ fixtures.

**GOV.UK Frontend’s own stack:** Frontend ships as a **Node** package with **Nunjucks** macros and official `fixtures.json`. Use Node for install, Sass, and optional Nunjucks freshness checks only.

**Guidance rule:** Prefer searching the URLs in [`docs/guidance-sources.md`](docs/guidance-sources.md) over inventing local policy.

**Documentation rule:** Every change must leave dual-audience docs updated. Detail: [`docs/documentation-structure.md`](docs/documentation-structure.md).

## Non-negotiables

1. **GOV.UK Frontend macros are the HTML source of truth** — native PHP renderers track those macros. Do **not** copy-paste component HTML long-term, and do **not** shell out to Node to render HTML.
2. **PHP HTML must match every official fixture** — byte-for-byte (`Renderer::render() === fixture html` after trimming outer whitespace of renderer output only). DomCrawler is for failure diffs only. No normalisation; never edit fixture `html`. See [`docs/testing-components.md`](docs/testing-components.md).
3. **No frontend UI frameworks** — no React/Vue/Angular/Svelte for GOV.UK UI; PHP + GOV.UK Frontend only.
4. **No ad-hoc custom CSS** — Sass pipeline in [`styles/`](styles/) → [`govuk-overrides.scss`](styles/govuk-overrides.scss) last. See [`docs/styles.md`](docs/styles.md).
5. **No `!important` in service CSS** — cascade order and specificity only.
6. **Components via library API** — pages use `Renderer::render()` / Blade composition; never hand-paste `govuk-*` markup.
7. **Patterns compose components** — no invented pattern fixture suites.
8. **WCAG 2.2 AA baseline** — skip link, one `h1`, visible focus, Error summary + field errors, `novalidate`.
9. **Progressive enhancement** — core tasks work without Frontend JS; keep `js-enabled` / `initAll()`.
10. **Do not ship unreleased GOV.UK chrome** — wait for Frontend release + fixtures.
11. **100% code coverage** — Pest with `--coverage --min=100` for `app/` (excluding `app/Models`). CI fails below that.
12. **Always review the latest release notes** before upgrading — https://github.com/alphagov/govuk-frontend/releases/latest — then [`docs/upgrading-govuk-frontend.md`](docs/upgrading-govuk-frontend.md).
13. **Performance and security baseline** — [`baseline/`](baseline/) via `BaselineHeaders` middleware. See [`docs/frontend-performance.md`](docs/frontend-performance.md) and [`docs/frontend-security.md`](docs/frontend-security.md).
14. **Split finished work into focused commits** with comprehensive messages.
15. **Document every change for humans and agents**.
16. **Follow current PHP / Laravel best practices** recorded in [`docs/tech-stack.md`](docs/tech-stack.md).

Using this repo does **not** make a service assessment-ready. See [`docs/service-assessment-readiness.md`](docs/service-assessment-readiness.md).

## Agent playbooks

| Task                                  | Doc                                                                    |
| ------------------------------------- | ---------------------------------------------------------------------- |
| Upgrade GOV.UK Frontend               | [`docs/upgrading-govuk-frontend.md`](docs/upgrading-govuk-frontend.md) |
| Add a component                       | [`docs/creating-components.md`](docs/creating-components.md)           |
| Add a pattern                         | [`docs/creating-patterns.md`](docs/creating-patterns.md)               |
| Layout / chrome                       | [`docs/layout-chrome.md`](docs/layout-chrome.md)                       |
| Fixture / parity testing              | [`docs/testing-components.md`](docs/testing-components.md)             |
| Page shell                            | [`docs/page-shell.md`](docs/page-shell.md)                             |
| Example service (journey + catalogue) | [`docs/example-service.md`](docs/example-service.md)                   |
| Deploy on Render                      | [`docs/deploying-on-render.md`](docs/deploying-on-render.md)           |
| Frontend performance                  | [`docs/frontend-performance.md`](docs/frontend-performance.md)         |
| Frontend security                     | [`docs/frontend-security.md`](docs/frontend-security.md)               |
| Accessibility                         | [`docs/accessibility.md`](docs/accessibility.md)                       |
| Content & forms                       | [`docs/content-and-forms.md`](docs/content-and-forms.md)               |
| Design tokens                         | [`docs/design-tokens.md`](docs/design-tokens.md)                       |
| Styles / Sass cascade                 | [`docs/styles.md`](docs/styles.md)                                     |
| Dual-audience documentation           | [`docs/documentation-structure.md`](docs/documentation-structure.md)   |
| Guidance sources                      | [`docs/guidance-sources.md`](docs/guidance-sources.md)                 |

## Quick page review

Before finishing a page change:

- [ ] Page template shell / before-content / single `h1` / title
- [ ] Library API only for GOV.UK UI blocks (not pasted HTML)
- [ ] Back link **or** breadcrumbs — not both
- [ ] Forms: `novalidate`, Error summary + messages, values retained
- [ ] Focus styles untouched; no `outline: none`
- [ ] Trusted/sanitised HTML only; prefer plain text options
- [ ] HTML responses use baseline security headers; assets use the matching cache kind
- [ ] CSS from the Sass pipeline in `<head>`; Frontend JS is external `type="module"`; `js-enabled` snippet matches pinned CSP hash
- [ ] No `!important` in service styles; overrides only via `govuk-overrides.scss` specificity
- [ ] Coverage remains 100% for touched `app/` code
- [ ] Pest parity suite green: PHP HTML ≡ every fixture `html`
- [ ] Dual-audience docs updated
- [ ] Code follows PHP / Laravel practices in [`docs/tech-stack.md`](docs/tech-stack.md)

## Watching upstream

**Before every Frontend upgrade:** read https://github.com/alphagov/govuk-frontend/releases/latest.

Upgrade only via [`docs/upgrading-govuk-frontend.md`](docs/upgrading-govuk-frontend.md).

Local start: `npm start`. Verify: `npm run verify`.
