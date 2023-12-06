<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException as Exception;

class ResourceValidationException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $data = [
            'errors' => [],
        ];

        foreach ($this->errors() as $field => $errors) {
            foreach ($errors as $error) {
                $data['errors'][] = [
                    'status'    => '422',

                    'title'     => 'Unprocessable Content',
                    'detail'    => $error,

                    'source' => [
                        'pointer'   => '/' . str_replace('.', '/', $field),
                    ],
                ];
            }
        }

        return new JsonResponse($data, 422);
    }
}
