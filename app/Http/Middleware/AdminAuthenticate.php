<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

class AdminAuthenticate
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
        $GLOBALS["administrator"] = DB::table('administrator')
                ->where('idadministrator', $request->header('GSX-CODE'))
                ->where('token', $request->header('GSX-TOKEN'))->first();
        if ($GLOBALS["administrator"]) {
            return $next($request);
        }
        return new Response(["message" => "Not Authenticate"], 401);
    }
}
