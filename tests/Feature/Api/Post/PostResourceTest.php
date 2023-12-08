<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PostResourceTest extends TestCase
{
    use DatabaseMigrations;

    public function test_store_resource(): void
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
                'type'  => 'posts',

                'attributes' => [
                    'title'     => 'New post title!',
                    'content'   => 'New post content.',
                ],

                'relationships' => [
                    'writer' => [
                        'data' => [
                            'type'  => 'users',
                            'id'    => '1',
                        ],
                    ],
                ],
            ],
        ];

        $headers = [
            'Accept'        => 'application/vnd.api+json',
            'Content-Type'  => 'application/vnd.api+json',
        ];

        $response = $this->json('POST', 'api/posts', $data, $headers);

        $response->assertStatus(201);
        $response->assertHeader('Location', URL::route('posts.show', 1));
        $response->assertJson([
            'data' => [
                'type'  => 'posts',

                'attributes' => [
                    'title'     => 'New post title!',
                    'content'   => 'New post content.',
                ],
            ],

            'links' => [
                'self'  => URL::route('posts.show', 1),
            ],
        ]);

        $this->assertDatabaseHas('posts', [
            'id'        => 1,
            'title'     => 'New post title!',
            'content'   => 'New post content.',
            'writer_id' => 1,
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

        $post = new Post([
            'title'     => 'New post title!',
            'content'   => 'New post content.',
        ]);

        $post->writer()
            ->associate($user);

        $post->save();

        $headers = [
            'Accept'    => 'application/vnd.api+json',
        ];

        $response = $this->json('GET', 'api/posts/1', [], $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'type'  => 'posts',
                'id'    => '1',

                'attributes' => [
                    'title'     => 'New post title!',
                    'content'   => 'New post content.',
                ],
            ],

            'links' => [
                'self'  => URL::route('posts.show', 1),
            ],
        ]);
    }

    public function test_update_resource(): void
    {
        $oldUser = new User([
            'name'      => 'Rafael Indriago',
            'email'     => 'rafael.indriago93@gmail.com',
            'password'  => 'rafael.indriago93',
            'type'      => 'writer',
        ]);

        $oldUser->save();

        $post = new Post([
            'title'     => 'New post title!',
            'content'   => 'New post content.',
        ]);

        $post->writer()
            ->associate($oldUser);

        $post->save();

        $newUser = new User([
            'name'      => 'Andres Moya',
            'email'     => 'rafael.indriago.58321@gmail.com',
            'password'  => 'rafael.indriago.58321',
            'type'      => 'writer',
        ]);

        $newUser->save();

        $data = [
            'data' => [
                'type'  => 'posts',
                'id'    => '1',

                'attributes' => [
                    'title'     => 'Updated post title!',
                    'content'   => 'Updated post content.',
                ],

                'relationships' => [
                    'writer' => [
                        'data' => [
                            'type'  => 'users',
                            'id'    => '2',
                        ],
                    ],
                ],
            ],
        ];

        $headers = [
            'Accept'        => 'application/vnd.api+json',
            'Content-Type'  => 'application/vnd.api+json',
        ];

        $response = $this->json('PATCH', 'api/posts/1', $data, $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'type'  => 'posts',
                'id'    => '1',

                'attributes' => [
                    'title'     => 'Updated post title!',
                    'content'   => 'Updated post content.',
                ],
            ],

            'links' => [
                'self'  => URL::route('posts.show', 1),
            ],
        ]);

        $this->assertDatabaseHas('posts', [
            'id'        => 1,
            'title'     => 'Updated post title!',
            'content'   => 'Updated post content.',
            'writer_id' => 2,
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

        $post = new Post([
            'title'     => 'New post title!',
            'content'   => 'New post content.',
        ]);

        $post->writer()
            ->associate($user);

        $post->save();

        $headers = [
            'Accept'    => 'application/vnd.api+json',
        ];

        $response = $this->json('DELETE', 'api/posts/1', [], $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [],
        ]);

        $this->assertDatabaseMissing('posts', [
            'id'        => 1,
            'title'     => 'New post title!',
            'content'   => 'New post content.',
            'writer_id' => 1,
        ]);
    }
}
