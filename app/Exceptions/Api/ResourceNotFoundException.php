<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceNotFoundException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $data = [
            'errors' => [
                [
                    'status'    => '404',

                    'title'     => 'Not found',
                    'detail'    => 'The resource was not found in the storage.',
                ],
            ],
        ];

        return new JsonResponse($data, 404);
    }
}
