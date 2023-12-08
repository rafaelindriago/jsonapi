<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\Api\ResourceIdException;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class ValidateResourceId
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string $type, ?string $parameter = null): Response
    {
        $parameter = $parameter ?: Str::of($type)
            ->singular()
            ->toString();

        $id = $request->route($parameter) instanceof Model
            ? $request->route($parameter)->getKey()
            : $request->route($parameter);

        if ($request->input('data.id') !== (string) $id) {
            throw new ResourceIdException();
        }

        return $next($request);
    }
}
