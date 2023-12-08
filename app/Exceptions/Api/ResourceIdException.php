<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceIdException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $data = [
            'errors' => [
                [
                    'status'    => '409',

                    'title'     => 'Conflict',
                    'detail'    => "The resource id must be equal to the id passed to the endpoint.",

                    'source' => [
                        'pointer'   => '/data/id',
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 409);
    }
}
