<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterFormatException extends Exception
{
    /**
     * The field that do not allow the filter.
     *
     * @var string
     */
    protected $field;

    /**
     * The filter intended to apply.
     *
     * @var string
     */
    protected $filter;

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
                    'detail'    => 'The value of the filter has a wrong format.',

                    'source' => [
                        'parameter' => "filter[{$this->field}][{$this->filter}]",
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }

    /**
     * Set the field that do not allow the filter.
     */
    public function setField(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Set the filter intended to apply.
     */
    public function setFilter(string $filter): static
    {
        $this->filter = $filter;

        return $this;
    }
}
