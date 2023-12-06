<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\Api\ResourceTypeException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateResourceType
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        if ($request->input('data.type') !== $type) {
            $resourceTypeException = new ResourceTypeException();

            throw $resourceTypeException->setType($type);
        }

        return $next($request);
    }
}
