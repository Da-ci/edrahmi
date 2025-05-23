<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Resources\Api\ErrorResource;
use Symfony\Component\HttpFoundation\Response;

class ValidatePartnerApiOrigin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $partner = $request->partner;
        if (app()->environment('production')) {
            $origin = $request->header('Origin') ?? $request->header('Referer');

            if ($origin && rtrim($origin, '/') !== rtrim($partner->website_url, '/')) {
                return (new ErrorResource([
                    'http_code' => 403,
                    'code' => 'UNAUTHORIZED_ORIGIN',
                    'message' => 'Unauthorized origin',
                    'detail' => null,
                    'meta' => []
                ]))->response();
            }
        }

        return $next($request);
    }
}
