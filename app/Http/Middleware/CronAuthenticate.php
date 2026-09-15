<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CronAuthenticate
{
    /**
     * Protect endpoints meant to be triggered by a scheduled job.
     *
     * The caller must send the shared secret configured in PUSH_CHECK_TOKEN
     * through the GSX-CRON-TOKEN header. When no secret is configured the
     * endpoint is disabled instead of being left open.
     */
    public function handle(Request $request, Closure $next)
    {
        $expected = (string) config('services.push.cron_token', '');
        $provided = (string) $request->header('GSX-CRON-TOKEN', '');

        if ($expected === '' || $provided === '' || ! hash_equals($expected, $provided)) {
            return new Response(['message' => 'Not Authenticate'], 401);
        }

        return $next($request);
    }
}
