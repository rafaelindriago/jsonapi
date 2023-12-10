<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceFilterException extends Exception
{
    /**
     * The non filterable field.
     *
     * @var string
     */
    protected $field;

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
                    'detail'    => empty(trim($this->field)) === false
                                    ? "The field {$this->field} is not filterable."
                                    : 'The filter parameter is malformed.',

                    'source' => [
                        'parameter' => 'filter',
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }

    /**
     * Set the non filterable field.
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }
}
