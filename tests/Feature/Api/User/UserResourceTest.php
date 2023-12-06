<?php

declare(strict_types=1);

namespace Tests\Feature\Api\User;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    public function test_store_resource(): void
    {
        $data = [
            'data' => [
                'type'  => 'users',

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

        $response->assertStatus(201);
        $response->assertHeader('Location', URL::route('users.show', 1));

        $this->assertDatabaseHas('users', [
            'email' => 'rafael.indriago93@gmail.com',
        ]);
    }

    public function test_show_resource(): void
    {
        $user = new User([
            'name'      => 'Rafael Indriago',
            'email'     => 'rafael.indriago93@gmail.com',
            'password'  => 'rafael.indriago93',
            'type'      => 'writer',
        ]);

        $user->save();

        $headers = [
            'Accept'    => 'application/vnd.api+json',
        ];

        $response = $this->json('GET', 'api/users/1', [], $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'type'  => 'users',
                'id'    => 1,

                'attributes' => [
                    'name'      => 'Rafael Indriago',
                    'email'     => 'rafael.indriago93@gmail.com',
                    'type'      => 'writer',
                ],
            ],
            'links' => [
                'self'  => URL::route('users.show', 1),
            ],
        ]);
    }

    public function test_update_resource(): void
    {
        $user = new User([
            'name'      => 'Rafael Indriago',
            'email'     => 'rafael.indriago93@gmail.com',
            'password'  => 'rafael.indriago93',
            'type'      => 'writer',
        ]);

        $user->save();

        $data = [
            'data' => [
                'type'  => 'users',
                'id'    => 1,

                'attributes' => [
                    'name'      => 'Andres Moya',
                ],
            ],
        ];

        $headers = [
            'Accept'        => 'application/vnd.api+json',
            'Content-Type'  => 'application/vnd.api+json',
        ];

        $response = $this->json('PATCH', 'api/users/1', $data, $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'type'  => 'users',
                'id'    => 1,

                'attributes' => [
                    'name'      => 'Andres Moya',
                    'email'     => 'rafael.indriago93@gmail.com',
                    'type'      => 'writer',
                ],
            ],
            'links' => [
                'self'  => URL::route('users.show', 1),
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name'  => 'Andres Moya',
            'email' => 'rafael.indriago93@gmail.com',
        ]);
    }

    public function test_delete_resource(): void
    {
        $user = new User([
            'name'      => 'Rafael Indriago',
            'email'     => 'rafael.indriago93@gmail.com',
            'password'  => 'rafael.indriago93',
            'type'      => 'writer',
        ]);

        $user->save();

        $headers = [
            'Accept'    => 'application/vnd.api+json',
        ];

        $response = $this->json('DELETE', 'api/users/1', [], $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [],
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => 'rafael.indriago93@gmail.com',
        ]);
    }
}
