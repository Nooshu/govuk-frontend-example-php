# Example service

**Apply for a fishing rod licence** is the reference GOV.UK service in this repository. It is an example. It does not take payment, send email, or issue a licence.

Every HTML page shows an **Important** notification banner (“This is a live demo. It is not a real government service.”), styled yellow via `.app-demo-banner`, and a phase banner that repeats that it is a demonstration. Search engines are told to stay away: `meta robots` and `X-Robots-Tag` are `noindex, nofollow`, and `/robots.txt` disallows all paths.

Pages are **PHP / Laravel / Blade**. Component HTML comes from **native PHP renderers** that track GOV.UK Frontend macros and match every official fixture. The pin is **6.5.1**. See [tech-stack.md](tech-stack.md).

## Run it

```sh
npm ci
composer install
npm start
```

Opens at <http://127.0.0.1:8000>. Set `PORT` to use another port.

Public demo hosting: [deploying-on-render.md](deploying-on-render.md).

`APP_ENV=production` hides the component catalogue unless `DEMOS_ENABLED=true` (set on the Render demo). The licence journey stays available.

## Start to confirmation

1. Start at `/` (English) or `/cy` (Welsh start page only). Choose **Start now**.
2. Answer the questions in order: licence length, full name, date of birth, where you will fish, and email.
3. Check your answers at `/check-answers`. Change links return to a question.
4. Accept and continue. The confirmation page at `/confirmation` shows an example reference.

Invalid answers stay on the same question, with an error summary and the values you entered. You cannot open confirmation until the questions are complete.

## Pages

| Path                         | What it shows                                                                |
| ---------------------------- | ---------------------------------------------------------------------------- |
| `/` and `/cy`                | Start page                                                                   |
| `/licence-length` … `/email` | Question pages, then check your answers and confirmation                     |
| `/components`                | Component catalogue (links only) when demos are on                           |
| `/components/{name}`         | One component: PHP HTML for a fixture, parity banner only when match is true |
| `/components/{name}/fixture` | Raw HTML fragment                                                            |

## Tests

```sh
npm test
```

Component tests render **every** official fixture. The comparison is PHP `Renderer` output against the fixture `html` string.

## Limits

- Sessions are stored on disk/memory and end when the process stops.
- The password is checked and then discarded.
- An upload stores the file name only (PDF, PNG, or JPG).
- Using this repo does not make a service assessment-ready. See [service-assessment-readiness.md](service-assessment-readiness.md).
