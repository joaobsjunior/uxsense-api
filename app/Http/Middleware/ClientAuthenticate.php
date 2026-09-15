<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClientAuthenticate
{
    /**
     * Authenticate a device (mobile client) through the GSX-DEVICE / GSX-TOKEN headers.
     *
     * Both headers are required and the stored token must be non-empty: after a
     * logout the device token is set to NULL and the previous implementation
     * matched those rows when the token header was omitted.
     */
    public function handle(Request $request, Closure $next)
    {
        $device = self::resolve($request);

        if ($device) {
            $GLOBALS['device'] = $device;
            $request->attributes->set('device', $device);

            return $next($request);
        }

        return new Response(['message' => 'Not Authenticate'], 401);
    }

    /**
     * Resolve the device identified by the request headers, if any.
     */
    public static function resolve(Request $request): ?object
    {
        $id = (string) $request->header('GSX-DEVICE', '');
        $token = (string) $request->header('GSX-TOKEN', '');

        if ($id === '' || $token === '' || ! ctype_digit($id)) {
            return null;
        }

        $device = DB::table('device')
            ->where('iddevice', (int) $id)
            ->whereNotNull('token')
            ->first();

        if (! $device || (string) $device->token === '' || ! hash_equals((string) $device->token, $token)) {
            return null;
        }

        return $device;
    }
}
