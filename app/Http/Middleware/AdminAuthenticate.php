<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class AdminAuthenticate
{
    /**
     * Authenticate an administrator through the GSX-CODE / GSX-TOKEN headers.
     *
     * Both headers are required and the stored token must be non-empty: the
     * previous implementation matched "token = NULL" rows when the header was
     * missing, which allowed logging in as any administrator without a token.
     */
    public function handle(Request $request, Closure $next)
    {
        $administrator = self::resolve($request);

        if ($administrator) {
            $GLOBALS['administrator'] = $administrator;
            $request->attributes->set('administrator', $administrator);

            return $next($request);
        }

        return new Response(['message' => 'Not Authenticate'], 401);
    }

    /**
     * Resolve the administrator identified by the request headers, if any.
     */
    public static function resolve(Request $request): ?object
    {
        $code = (string) $request->header('GSX-CODE', '');
        $token = (string) $request->header('GSX-TOKEN', '');

        if ($code === '' || $token === '' || ! ctype_digit($code)) {
            return null;
        }

        $administrator = DB::table('administrator')
            ->where('idadministrator', (int) $code)
            ->whereNotNull('token')
            ->first();

        if (! $administrator || (string) $administrator->token === '' || ! hash_equals((string) $administrator->token, $token)) {
            return null;
        }

        return $administrator;
    }
}
