# Project purpose

This repository is a **PHP / Laravel** example of a **GDS-compliant** government frontend.

## Intent

This line demonstrates that teams can:

1. Meet [Service Standard](https://www.gov.uk/service-manual/service-standard) and [Technology Code of Practice](https://www.gov.uk/guidance/the-technology-code-of-practice) expectations for common components, accessibility, and open standards — as far as the UI layer can.
2. Use **PHP 8.4+**, **Laravel 12**, and **Blade** for HTML generation and application logic.
3. Use **[GOV.UK Frontend](https://frontend.design-system.service.gov.uk/)** (pinned release **6.5.1**) as the **only** frontend component library — styles, progressive-enhancement JS, and macro-driven HTML contracts.
4. Do **not** introduce SPA or component **frontend frameworks** (React, Vue, Angular, Svelte, Next.js UI layers, etc.) for rendering GOV.UK UI.
5. Derive component HTML from **native PHP renderers** that track GOV.UK Frontend macros / `template.njk` — never Nunjucks or Node at request time — and wire **official test fixtures** for extensive **100% HTML parity** testing of PHP output.

## Priorities

1. Frontend web performance
2. Frontend security
3. Reduced maintenance
4. Accessibility
5. Inclusive design

See [priorities.md](priorities.md).

GOV.UK Frontend remains a **Node** package with **Nunjucks** macros upstream (install, Sass, fixture contract). See [tech-stack.md](tech-stack.md).

## What “GDS compliant” means here

Follow official GDS guidance and the Design System / Frontend contracts rather than inventing parallel UI systems. Canonical places to search: [guidance-sources.md](guidance-sources.md).

Using this example does **not** by itself make a live service assessment-ready — see [service-assessment-readiness.md](service-assessment-readiness.md) and [assisted digital](https://www.gov.uk/service-manual/helping-people-to-use-your-service/assisted-digital-support-introduction).

## Agent skill

Coding agents should apply [`.cursor/skills/gds-compliant-frontend/SKILL.md`](../.cursor/skills/gds-compliant-frontend/SKILL.md) when scaffolding or reviewing work in this repo.
