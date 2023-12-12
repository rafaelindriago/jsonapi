<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterMalformedException extends Exception
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
                    'detail'    => 'The filter parameter is malformed.',

                    'source' => [
                        'parameter' => "filter",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }
}
