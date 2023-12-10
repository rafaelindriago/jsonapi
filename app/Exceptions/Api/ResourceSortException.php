<?php

declare(strict_types=1);

namespace App\Exceptions\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceSortException extends Exception
{
    /**
     * The non sortable field.
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
                                    ? "The field {$this->field} is not sortable."
                                    : 'The sort parameter is malformed.',

                    'source' => [
                        'parameter' => 'sort',
                    ],
                ],
            ],
        ];

        return new JsonResponse($data, 400);
    }

    /**
     * Set the non sortable field.
     */
    public function setField(string $field)
    {
        $this->field = $field;

        return $this;
    }
}
