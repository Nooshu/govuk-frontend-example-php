<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Baseline\Policy;
use App\Support\Assets;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class AssetController extends Controller
{
    public function __construct(private readonly Assets $assets, private readonly Policy $policy) {}

    public function show(Request $request, string $path): Response
    {
        $asset = $this->assets->resolve('/assets/'.$path);
        if ($asset === null) {
            abort(404);
        }
        $secure = $request->secure() || $request->headers->get('X-Forwarded-Proto') === 'https';
        $headers = $this->policy->buildHeaders($asset['kind'], $secure, false, [], $asset['contentType']);
        foreach ($this->policy->headersToRemove() as $name) {
            // response constructor applies headers below
        }
        unset($headers['X-Baseline-Remove-Server']); // cleanup placeholders if any
        $clean = [];
        foreach ($headers as $k => $v) {
            if (! str_starts_with($k, 'X-Baseline-Remove-')) {
                $clean[$k] = $v;
            }
        }

        return response($asset['body'], 200, $clean);
    }
}
