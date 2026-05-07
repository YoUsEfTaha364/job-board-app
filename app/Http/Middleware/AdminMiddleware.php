<?php

namespace App\Http\Middleware;

use App\Services\ApiResponseService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class AdminMiddleware
{
     
    public function handle(Request $request, Closure $next): Response
    {
        if($request->user()->role!="admin"){
            return ApiResponseService::Response(403,"unauthorized",[]);
        }
        return $next($request);
    }
}
