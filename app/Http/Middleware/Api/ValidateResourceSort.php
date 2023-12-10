<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\Api\ResourceSortException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateResourceSort
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, array|string ...$sortable): Response
    {
        if ($request->filled('sort')) {
            $requestedSortings = $request->string('sort')
                ->explode(',')
                ->transform(
                    fn(string $requestedSort) => mb_substr($requestedSort, 0, 1) === '-'
                        ? mb_substr($requestedSort, 1)
                        : $requestedSort
                );

            foreach ($requestedSortings as $requestedSort) {
                if ( ! in_array($requestedSort, $sortable)) {
                    $resourceSortException = new ResourceSortException();

                    throw $resourceSortException->setField($requestedSort);
                }
            }
        }

        return $next($request);
    }
}
