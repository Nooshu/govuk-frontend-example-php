<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Baseline\Policy;
use App\Support\Assets;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class BaselineHeaders
{
    public function __construct(
        private readonly Policy $policy,
        private readonly Assets $assets,
    ) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next, string $kind = Policy::KIND_DOCUMENT): Response
    {
        $response = $next($request);

        $secure = $request->secure() || $request->headers->get('X-Forwarded-Proto') === 'https';
        $setsCookie = $response->headers->has('Set-Cookie');
        $preload = $this->policy->isDocument($kind) ? $this->assets->preloads() : [];

        foreach ($this->policy->headersToRemove() as $name) {
            $response->headers->remove($name);
        }

        $headers = $this->policy->buildHeaders($kind, $secure, $setsCookie, $preload);
        foreach ($headers as $name => $value) {
            if (str_starts_with($name, 'X-Baseline-Remove-')) {
                continue;
            }
            $response->headers->set($name, $value);
        }

        if ($this->policy->isDocument($kind)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            if ($kind === Policy::KIND_DOCUMENT && ! $response->headers->has('ETag')) {
                $body = $response->getContent();
                if (is_string($body)) {
                    $response->headers->set('ETag', Policy::strongEtag($body));
                }
            }
        }

        return $response;
    }
}
