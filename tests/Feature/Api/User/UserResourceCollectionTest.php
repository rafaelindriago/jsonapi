<?php

declare(strict_types=1);

namespace Tests\Feature\Api\User;

use App\Models\User;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserResourceCollectionTest extends TestCase
{
    public function test_index_resource_collection(): void
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

        $response = $this->json('GET', 'api/users', [], $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'users',
                    'id'    => 1,

                    'attributes' => [
                        'name'      => 'Rafael Indriago',
                        'email'     => 'rafael.indriago93@gmail.com',
                        'type'      => 'writer',
                    ],
                ],
            ],

            'links' => [
                'self'  => URL::route('users.index'),
            ],
        ]);
    }
}
