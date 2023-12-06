<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use App\Exceptions\Api\ResourceNotFoundException;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Routing\Middleware\SubstituteBindings as Middleware;
use Symfony\Component\HttpFoundation\Response;

class SubstituteBindings extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle($request, Closure $next): Response
    {
        $route = $request->route();

        try {
            $this->router->substituteBindings($route);

            $this->router->substituteImplicitBindings($route);
        } catch (Exception $exception) {
            if ($route->getMissing()) {
                return $route->getMissing()($request, $exception);
            }

            $resourceNotFoundException = new ResourceNotFoundException();

            throw $resourceNotFoundException->setModel($exception->getModel(), $exception->getIds());
        }

        return $next($request);
    }
}
