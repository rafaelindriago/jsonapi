<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Tests\TestCase;

class ResponseTest extends TestCase
{
    public function test_request_not_acceptable_exception_response(): void
    {
        $response = $this->json('GET', 'api/users');

        $response->assertStatus(406);
        $response->assertJson([
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
        ]);
    }

    public function test_request_unsupported_media_type_exception_response(): void
    {
        $headers = [
            'Accept'        => 'application/vnd.api+json',
        ];

        $response = $this->json('POST', 'api/users', [], $headers);

        $response->assertStatus(415);
        $response->assertJson([
            'errors' => [
                [
                    'status'    => '415',

                    'title'     => 'Unsupported Media Type',
                    'detail'    => 'The Content-Type header of the request must be application/vnd.api+json.',

                    'source' => [
                        'header'    => 'Content-Type',
                    ],
                ],
            ],
        ]);
    }

    public function test_resource_type_exception_response(): void
    {
        $data = [
            'data' => [
                'type'  => 'people',

                'attributes' => [
                    'name'      => 'Rafael Indriago',
                    'email'     => 'rafael.indriago93@gmail.com',
                    'password'  => 'rafael.indriago93',
                    'type'      => 'writer',
                ],
            ],
        ];

        $headers = [
            'Accept'        => 'application/vnd.api+json',
            'Content-Type'  => 'application/vnd.api+json',
        ];

        $response = $this->json('POST', 'api/users', $data, $headers);

        $response->assertStatus(409);
        $response->assertJson([
            'errors' => [
                [
                    'status'    => '409',

                    'title'     => 'Conflict',
                    'detail'    => "The resource type for this endpoint must be users.",

                    'source' => [
                        'pointer'   => '/data/type',
                    ],
                ],
            ],
        ]);
    }

    public function test_resource_not_found_exception_response(): void
    {
        $headers = [
            'Accept'        => 'application/vnd.api+json',
        ];

        $response = $this->json('GET', 'api/users/1', [], $headers);

        $response->assertStatus(404);
        $response->assertJson([
            'errors' => [
                [
                    'status'    => '404',

                    'title'     => 'Not found',
                    'detail'    => 'The resource was not found in the storage.',
                ],
            ],
        ]);
    }

    public function test_resource_validation_exception_response(): void
    {
        $data = [
            'data' => [
                'type'  => 'users',

                'attributes' => [
                    'name'      => 'Rafael Indriago',
                    'email'     => 'rafael.indriago93@gmail.com',
                    'password'  => '',
                    'type'      => 'writer',
                ],
            ],
        ];

        $headers = [
            'Accept'        => 'application/vnd.api+json',
            'Content-Type'  => 'application/vnd.api+json',
        ];

        $response = $this->json('POST', 'api/users', $data, $headers);

        $response->assertStatus(422);
        $response->assertJson([
            'errors' => [
                [
                    'status'    => '422',

                    'title'     => 'Unprocessable Content',
                    'detail'    => 'The password field is required.',

                    'source' => [
                        'pointer'   => '/data/attributes/password',
                    ],
                ],
            ],
        ]);
    }
}
