<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterOperatorNotAllowedException extends Exception
{
    /**
     * The field that do not allow the filter opeator.
     *
     * @var string
     */
    protected $field;

    /**
     * The filter opetator intended to apply.
     *
     * @var string
     */
    protected $operator;

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
                    'detail'    => 'The filter operator is not allowed for this field.',

                    'source' => [
                        'parameter' => "filter[{$this->field}][{$this->operator}]",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }

    /**
     * Set the field that do not allow the filter operator.
     */
    public function setField(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Set the filter operator intended to apply.
     */
    public function setOperator(string $operator): static
    {
        $this->operator = $operator;

        return $this;
    }
}
