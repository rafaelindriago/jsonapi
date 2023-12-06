<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\Api\RequestNotAcceptableException;
use App\Exceptions\Api\RequestUnsupportedMediaTypeException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentNegotiation
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->header('Accept') !== 'application/vnd.api+json') {
            throw new RequestNotAcceptableException();
        }

        if ($request->method() === 'POST' || $request->method() === 'PATCH') {
            if ($request->header('Content-Type') !== 'application/vnd.api+json') {
                throw new RequestUnsupportedMediaTypeException();
            }
        }

        return $next($request);
    }
}
