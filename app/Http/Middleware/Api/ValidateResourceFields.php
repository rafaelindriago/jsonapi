<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\Api\ResourceFieldException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateResourceFields
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $type, array|string ...$fields): Response
    {
        if ($request->has("fields.{$type}")) {
            $requestedFields = $request->string("fields.{$type}")
                ->explode(',');

            foreach ($requestedFields as $requestedField) {
                if ( ! in_array($requestedField, $fields)) {
                    $resourceFieldException = new ResourceFieldException();

                    throw $resourceFieldException->setType($type)
                        ->setField($requestedField);
                }
            }
        }

        return $next($request);
    }
}
