<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestNotFoundException extends Exception
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
                    'detail'    => 'The endpoint was not found.',
                ],
            ],
        ];

        return new JsonResponse($data, 404);
    }
}
