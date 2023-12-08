<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Post;

use App\Http\Controllers\Controller;
use App\Http\Queries\Post\PostQuery;
use App\Http\Requests\Api\Post\StorePostRequest;
use App\Http\Requests\Api\Post\UpdatePostRequest;
use App\Http\Resources\Post\PostResource;
use App\Http\Resources\Post\PostResourceCollection;
use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PostController extends Controller
{
    /**
     * Create a new controller.
     */
    public function __construct()
    {
        $this->authorizeResource(Post::class);

        $this->middleware('resource.type:posts')
            ->only(['store', 'update']);

        $this->middleware('resource.fields:posts,title,content,writer')
            ->only(['index']);

        $this->middleware('resourse.sort:posts,title')
            ->only(['index']);

        $this->middleware('resource.filter:posts,title')
            ->only(['index']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $builder = Post::query();

        $posts = PostQuery::make($builder)
            ->paginate();

        return PostResourceCollection::make($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): JsonResource
    {
        $attributes = $request->validated('data.attributes');

        $post = Post::make($attributes);

        $writer = $request->input('data.relationships.writer');

        $post->writer()
            ->associate($writer['data']['id']);

        $post->save();

        return PostResource::make($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): JsonResource
    {
        return PostResource::make($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResource
    {
        $attributes = $request->validated('data.attributes');

        $post->fill($attributes);

        if ($request->has('data.relationships.writer.data.id')) {
            $writer = $request->input('data.relationships.writer');

            $post->writer()
                ->associate($writer['data']['id']);
        }

        $post->save();

        return PostResource::make($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): JsonResource
    {
        $post->delete();

        return JsonResource::make([]);
    }
}
