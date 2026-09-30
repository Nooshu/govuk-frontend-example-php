<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Fingerprinted stylesheet, Frontend JS, and initAll module URLs.
 */
final class Assets
{
    private string $cssHref;

    private string $appHref;

    private string $scriptHref;

    private string $cssBody;

    private string $scriptBody;

    private string $appBody;

    private string $frontendAssets;

    /** @var list<array{href: string, as: string, type?: string}> */
    private array $preloads;

    public function __construct(
        ?string $stylesheetPath = null,
        ?string $govukRoot = null,
        ?string $frontendAssets = null,
    ) {
        $stylesheetPath ??= base_path('dist/stylesheets/application.css');
        $govukRoot ??= base_path('node_modules/govuk-frontend/dist/govuk');
        $frontendAssets ??= base_path('node_modules/govuk-frontend/dist/govuk/assets');

        if (! is_file($stylesheetPath)) {
            throw new \RuntimeException("missing {$stylesheetPath}, run npm run build:styles first");
        }
        $css = file_get_contents($stylesheetPath);
        if ($css === false) { // @codeCoverageIgnore
            throw new \RuntimeException("missing {$stylesheetPath}, run npm run build:styles first"); // @codeCoverageIgnore
        }
        $scriptPath = $govukRoot.'/govuk-frontend.min.js';
        if (! is_file($scriptPath)) {
            throw new \RuntimeException("missing {$scriptPath}");
        }
        $script = file_get_contents($scriptPath);
        if ($script === false) { // @codeCoverageIgnore
            throw new \RuntimeException("missing {$scriptPath}"); // @codeCoverageIgnore
        }

        $this->cssBody = $css;
        $this->scriptBody = $script;
        $this->cssHref = '/assets/application.'.self::fingerprint($css).'.css';
        $this->scriptHref = '/assets/govuk-frontend.'.self::fingerprint($script).'.min.js';
        $this->appBody = "import { initAll } from '{$this->scriptHref}';\n\ninitAll();\n";
        $this->appHref = '/assets/app.'.self::fingerprint($this->appBody).'.mjs';
        $this->frontendAssets = $frontendAssets;
        $this->preloads = self::fontPreloads($css);
    }

    public function stylesheetHref(): string
    {
        return $this->cssHref;
    }

    public function appModuleHref(): string
    {
        return $this->appHref;
    }

    /**
     * @return list<array{href: string, as: string, type?: string}>
     */
    public function preloads(): array
    {
        return $this->preloads;
    }

    /**
     * @return array{body: string, contentType: string, kind: string}|null
     */
    public function resolve(string $path): ?array
    {
        $path = '/'.ltrim($path, '/');
        if ($path === $this->cssHref) {
            return ['body' => $this->cssBody, 'contentType' => 'text/css; charset=utf-8', 'kind' => 'fingerprinted-asset'];
        }
        if ($path === $this->scriptHref) {
            return ['body' => $this->scriptBody, 'contentType' => 'text/javascript; charset=utf-8', 'kind' => 'fingerprinted-asset'];
        }
        if ($path === $this->appHref) {
            return ['body' => $this->appBody, 'contentType' => 'text/javascript; charset=utf-8', 'kind' => 'fingerprinted-asset'];
        }
        if (str_starts_with($path, '/assets/')) {
            $rel = substr($path, strlen('/assets/'));
            if (str_contains($rel, '..')) {
                return null;
            }
            $file = $this->frontendAssets.'/'.$rel;
            if (! is_file($file)) {
                // also try fonts/images at root of assets
                return null;
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $type = match ($ext) {
                'css' => 'text/css; charset=utf-8',
                'js', 'mjs' => 'text/javascript; charset=utf-8',
                'woff2' => 'font/woff2',
                'woff' => 'font/woff',
                'svg' => 'image/svg+xml',
                'png' => 'image/png',
                'ico' => 'image/x-icon',
                'json' => 'application/json; charset=utf-8',
                default => 'application/octet-stream',
            };
            $body = file_get_contents($file);
            if ($body === false) { // @codeCoverageIgnore
                return null; // @codeCoverageIgnore
            }
            $kind = preg_match('/-[a-f0-9]{8,}-/', $rel) === 1 || str_contains($rel, '.woff')
                ? 'fingerprinted-asset'
                : 'static-asset';

            return ['body' => $body, 'contentType' => $type, 'kind' => $kind];
        }

        return null;
    }

    private static function fingerprint(string $body): string
    {
        return substr(hash('sha256', $body), 0, 16);
    }

    /**
     * @return list<array{href: string, as: string, type?: string}>
     */
    private static function fontPreloads(string $css): array
    {
        preg_match_all('#url\(\s*[\'"]?(/assets/fonts/[^\'")\s]+\.woff2)[\'"]?\s*\)#', $css, $matches);
        $links = [];
        foreach (array_unique($matches[1]) as $href) {
            $links[] = ['href' => $href, 'as' => 'font', 'type' => 'font/woff2'];
        }

        return $links;
    }
}
