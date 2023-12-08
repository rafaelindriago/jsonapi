<?php

declare(strict_types=1);

namespace Tests\Feature\Api\Post;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PostResourceCollectionTest extends TestCase
{
    use DatabaseMigrations;

    public function test_index_resource_collection(): void
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

        $response = $this->json('GET', 'api/posts', [], $headers);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'type'  => 'posts',
                    'id'    => '1',

                    'attributes' => [
                        'title'     => 'New post title!',
                        'content'   => 'New post content.',
                    ],
                ],
            ],

            'links' => [
                'self'  => URL::route('posts.index'),
            ],
        ]);
    }
}
