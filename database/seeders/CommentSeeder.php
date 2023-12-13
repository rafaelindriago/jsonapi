<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->get();
        $posts = Post::query()->whereNotNull('published_at')->get();

        foreach ($posts as $post) {
            Comment::factory(5)->for($post)->for($users->random(), 'writer')->create();
        }
    }
}
