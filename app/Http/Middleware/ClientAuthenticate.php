<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class ClientAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $GLOBALS["device"] = DB::table('device')
                ->where('iddevice', $request->header('GSX-DEVICE'))
                ->where('token', $request->header('GSX-TOKEN'))->first();
        if ($GLOBALS["device"]) {
            return $next($request);
        }
        return new Response(["message" => "Not Authenticate"], 401);
    }
}
