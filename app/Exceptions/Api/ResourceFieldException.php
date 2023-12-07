<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceFieldException extends Exception
{
    /**
     * The resource type.
     *
     * @var string
     */
    protected $type;

    /**
     * The non existing field.
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
                                    ? "The requested field {$this->field} does not exists."
                                    : "The fields[{$this->type}] parameter is empty or malformed.",

                    'source' => [
                        'parameter' => "fields[{$this->type}]",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }

    /**
     * Set the resource type.
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the non existing field.
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }
}
