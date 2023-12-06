<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceTypeException extends Exception
{
    /**
     * The expected resource type.
     *
     * @var string
     */
    protected $type;

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
                    'detail'    => "The resource type for this endpoint must be {$this->type}.",

                    'source' => [
                        'pointer'   => '/data/type',
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 409);
    }

    /**
     * Set the expected resource type for the endpoint.
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
