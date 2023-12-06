<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RequestNotAcceptableException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $data = [
            'errors' => [
                [
                    'status'    => '406',

                    'title'     => 'Not Acceptable',
                    'detail'    => 'The Accept header of the request must be application/vnd.api+json.',

                    'source' => [
                        'header'    => 'Accept',
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 406);
    }
}
