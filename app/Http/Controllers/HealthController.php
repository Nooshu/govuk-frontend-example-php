<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Response;

final class HealthController extends Controller
{
    public function __invoke(): Response
    {
        return response('ok', 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
