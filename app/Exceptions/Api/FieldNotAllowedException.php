<?php

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FieldNotAllowedException extends Exception
{
    /**
     * The type of resource intended to be requested.
     * 
     * @var string 
     */
    protected $type;

    /**
     * The field that is not allowed to be requested.
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
                    'detail'    => "The field {$this->field} is not allowed to be requested.",

                    'source' => [
                        'parameter' => "fields[{$this->type}]",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }

    /**
     * Set the type of the resource intended to be requested.
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the field that is not allowed to sort.
     */
    public function setField(string $field): static
    {
        $this->field = $field;

        return $this;
    }
}
