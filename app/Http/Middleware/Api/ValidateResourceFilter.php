<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\Api\ResourceFilterException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateResourceFilter
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $type, array|string ...$filterable): Response
    {
        if ($request->has("filter.{$type}")) {
            $requestedFilters = $request->collect("filter.{$type}")
                ->keys();

            foreach ($requestedFilters as $requestedFilter) {
                if ( ! in_array($requestedFilter, $filterable)) {
                    $resourceFilterException = new ResourceFilterException();

                    throw $resourceFilterException->setType($type)
                        ->setField($requestedFilter);
                }
            }
        }

        return $next($request);
    }
}
