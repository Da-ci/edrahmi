<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Application;
use Illuminate\Http\Request;
use App\Http\Resources\API\ErrorResource;
use Symfony\Component\HttpFoundation\Response;

class ValidateApplicationApiKeys
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $appKey = $request->header('x-app-key');
        $secretKey = $request->header('x-secret-key');

        if (!$appKey || !$secretKey) {
            return (new ErrorResource([
                'http_code' => 401,
                'code' => 'INVALID_API_KEYS',
                'message' => 'Invalid API keys',
                'detail' => null,
                'meta' => []
            ]))->response();
        }


        $application = Application::where('app_key', $appKey)
            ->where('app_secret', $secretKey)
            ->first();

        if (!$application) {
            return (new ErrorResource([
                'http_code' => 401,
                'code' => 'INVALID_API_KEYS',
                'message' => 'Invalid API keys',
                'detail' => null,
                'meta' => []
            ]))->response();
        }

        // $origin = $request->header('Origin') ?? $request->header('Referer');

        // if ($origin && rtrim($origin, '/') !== rtrim($application->website_url, '/')) {
        //     return (new ErrorResource([
        //         'http_code' => 403,
        //         'code' => 'UNAUTHORIZED_ORIGIN',
        //         'message' => 'Unauthorized origin',
        //         'detail' => null,
        //         'meta' => []
        //     ]))->response();
        // }

        return $next($request);



    }
}
