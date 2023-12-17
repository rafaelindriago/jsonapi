<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->where('type', '=', 'writer')->get();

        foreach ($users as $user) {
            Post::factory(3)->for($user, 'writer')->published()->create();
            Post::factory(2)->for($user, 'writer')->create();
        }
    }
}
