<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SortMalformedException extends Exception
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
                    'detail'    => 'The sort parameter is malformed.',

                    'source' => [
                        'parameter' => "sort",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }
}
