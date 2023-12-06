<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestUnsupportedMediaTypeException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $data = [
            'errors' => [
                [
                    'status'    => '415',

                    'title'     => 'Unsupported Media Type',
                    'detail'    => 'The Content-Type header of the request must be application/vnd.api+json.',

                    'source' => [
                        'header'    => 'Content-Type',
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 415);
    }
}
