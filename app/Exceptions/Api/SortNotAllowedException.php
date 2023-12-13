<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SortNotAllowedException extends Exception
{
    /**
     * The field that is not allowed to sort.
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
                    'detail'    => "The field {$this->field} is not allowed to sort.",

                    'source' => [
                        'parameter' => "sort",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
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
