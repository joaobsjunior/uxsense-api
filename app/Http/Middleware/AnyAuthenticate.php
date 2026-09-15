<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AnyAuthenticate
{
    /**
     * Accept either an authenticated administrator or an authenticated device.
     *
     * Used by read-only endpoints shared by the manager web app and the
     * mobile app (e.g. the capture technique list) that used to be public.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($administrator = AdminAuthenticate::resolve($request)) {
            $GLOBALS['administrator'] = $administrator;
            $request->attributes->set('administrator', $administrator);

            return $next($request);
        }

        if ($device = ClientAuthenticate::resolve($request)) {
            $GLOBALS['device'] = $device;
            $request->attributes->set('device', $device);

            return $next($request);
        }

        return new Response(['message' => 'Not Authenticate'], 401);
    }
}
