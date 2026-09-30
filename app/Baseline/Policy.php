<?php

declare(strict_types=1);

namespace App\Baseline;

/**
 * Loads baseline/policy.json and builds OWASP / cache response headers.
 */
final class Policy
{
    public const KIND_DOCUMENT = 'document';

    public const KIND_SENSITIVE_DOCUMENT = 'sensitive-document';

    public const KIND_FINGERPRINTED_ASSET = 'fingerprinted-asset';

    public const KIND_STATIC_ASSET = 'static-asset';

    /** @var array<string, mixed> */
    private array $data;

    public function __construct(?string $path = null)
    {
        $path ??= base_path('baseline/policy.json');
        if (! is_file($path)) {
            throw new \RuntimeException("cannot read baseline policy at {$path}");
        }
        $raw = file_get_contents($path);
        if ($raw === false) { // @codeCoverageIgnore
            throw new \RuntimeException("cannot read baseline policy at {$path}"); // @codeCoverageIgnore
        }
        /** @var array<string, mixed> $decoded */
        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $this->data = $decoded;
    }

    public function jsEnabledSnippet(): string
    {
        return (string) $this->data['jsEnabledSnippet'];
    }

    public function jsEnabledScriptHash(): string
    {
        return (string) $this->data['jsEnabledScriptHash'];
    }

    public function isDocument(string $kind): bool
    {
        return $kind === self::KIND_DOCUMENT || $kind === self::KIND_SENSITIVE_DOCUMENT;
    }

    /**
     * @param  list<array{href: string, as: string, type?: string}>  $preload
     * @return array<string, string>
     */
    public function buildHeaders(string $kind, bool $secureTransport, bool $setsCookie = false, array $preload = [], ?string $contentType = null): array
    {
        /** @var array<string, string> $cacheControlMap */
        $cacheControlMap = $this->data['cacheControl'];
        if (! isset($cacheControlMap[$kind])) {
            throw new \InvalidArgumentException("unknown response kind: {$kind}");
        }
        $document = $this->isDocument($kind);
        if ($setsCookie && ! $document) {
            throw new \InvalidArgumentException('Set-Cookie belongs on HTML documents');
        }

        $headers = [];
        foreach ($this->data['remove'] as $name) {
            // callers should remove these from the response
            $headers['X-Baseline-Remove-'.$name] = ''; // placeholder list via removeHeaders()
        }

        $resolvedType = $contentType ?? ($this->data['contentTypes'][$kind] ?? null);
        if (is_string($resolvedType) && $resolvedType !== '') {
            $headers['Content-Type'] = $resolvedType;
        }

        $cacheControl = $cacheControlMap[$kind];
        if ($setsCookie && $kind === self::KIND_DOCUMENT) {
            $cacheControl = 'private, no-cache';
        }
        $headers['Cache-Control'] = $cacheControl;

        foreach ($this->data['headers']['all'] as $name => $value) {
            $headers[$name] = $value;
        }
        if ($document) {
            foreach ($this->data['headers']['document'] as $name => $value) {
                $headers[$name] = $value;
            }
            $headers['Permissions-Policy'] = $this->permissionsPolicyHeader();
            $headers['Content-Security-Policy'] = $this->contentSecurityPolicy();
        }
        if ($secureTransport) {
            $hsts = $this->data['hsts'];
            $headers['Strict-Transport-Security'] = 'max-age='.$hsts['maxAge'].'; includeSubDomains';
        }
        $headers['Vary'] = 'Accept-Encoding';
        if ($preload !== []) {
            $headers['Link'] = $this->preloadLinkHeader($preload);
        }

        return $headers;
    }

    /**
     * @return list<string>
     */
    public function headersToRemove(): array
    {
        /** @var list<string> */
        return $this->data['remove'];
    }

    public function contentSecurityPolicy(): string
    {
        $parts = [];
        foreach ($this->data['csp']['directives'] as $name => $sources) {
            /** @var list<string> $sources */
            if ($sources === []) {
                $parts[] = $name;

                continue;
            }
            $merged = $sources;
            if ($name === 'script-src') {
                $merged[] = "'".$this->jsEnabledScriptHash()."'";
            }
            $parts[] = $name.' '.implode(' ', $merged);
        }

        return implode('; ', $parts);
    }

    public function permissionsPolicyHeader(): string
    {
        $features = [];
        foreach ($this->data['permissionsPolicy'] as $name) {
            $features[] = $name.'=()';
        }

        return implode(', ', $features);
    }

    /**
     * @param  list<array{href: string, as: string, type?: string}>  $links
     */
    public function preloadLinkHeader(array $links): string
    {
        $formatted = [];
        foreach ($links as $link) {
            $href = $link['href'];
            if (! str_starts_with($href, '/') || str_starts_with($href, '//')) {
                throw new \InvalidArgumentException("preload href must be same-origin path: {$href}");
            }
            $part = '<'.$href.'>; rel=preload; as='.$link['as'];
            if (isset($link['type'])) {
                $part .= '; type="'.$link['type'].'"';
            }
            if ($link['as'] === 'font') {
                $part .= '; crossorigin';
            }
            $formatted[] = $part;
        }

        return implode(', ', $formatted);
    }

    public static function strongEtag(string $body): string
    {
        return '"'.rtrim(strtr(base64_encode(hash('sha256', $body, true)), '+/', '-_'), '=').'"';
    }
}
