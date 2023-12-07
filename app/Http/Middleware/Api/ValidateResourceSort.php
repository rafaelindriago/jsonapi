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
    public function handle(Request $request, Closure $next, string $type, array|string ...$sortable): Response
    {
        if ($request->has("sort.{$type}")) {
            $requestedSorts = $request->string("sort.{$type}")
                ->explode(',')
                ->transform(
                    fn(string $requestedSort) => mb_substr($requestedSort, 0, 1) === '-'
                        ? mb_substr($requestedSort, 1)
                        : $requestedSort
                );

            foreach ($requestedSorts as $requestedSort) {
                if ( ! in_array($requestedSort, $sortable)) {
                    $resourceFieldException = new ResourceSortException();

                    throw $resourceFieldException->setType($type)
                        ->setField($requestedSort);
                }
            }
        }

        return $next($request);
    }
}
