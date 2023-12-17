<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldsMalformedException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $data = [
            'errors' => [
                [
                    'status'    => '400',

                    'title'     => 'Bad request',
                    'detail'    => 'The fields parameter is malformed.',

                    'source' => [
                        'parameter' => "fields",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }
}
