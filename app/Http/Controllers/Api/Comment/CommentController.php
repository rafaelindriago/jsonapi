<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Comment;

use App\Http\Controllers\Controller;
use App\Http\Queries\Comment\CommentQuery;
use App\Http\Requests\Api\Comment\StoreCommentRequest;
use App\Http\Requests\Api\Comment\UpdateCommentRequest;
use App\Http\Resources\Comment\CommentResource;
use App\Http\Resources\Comment\CommentResourceCollection;
use App\Models\Comment;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentController extends Controller
{
    /**
     * Create a new controller.
     */
    public function __construct()
    {
        $this->authorizeResource(Comment::class);

        $this->middleware('resource.type:comments')
            ->only(['store', 'update']);

        $this->middleware('resource.id:comment')
            ->only(['update']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CommentQuery $query): ResourceCollection
    {
        $comments = $query->paginate();

        return new CommentResourceCollection($comments);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request): JsonResource
    {
        $attributes = $request->validated('data.attributes');

        $comment = new Comment();

        $comment->fill($attributes);

        $post = $request->input('data.relationships.post.data.id');
        $writer = $request->input('data.relationships.writer.data.id');

        $comment->post()
            ->associate($post);

        $comment->writer()
            ->associate($writer);

        $comment->save();

        return new CommentResource($comment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment): JsonResource
    {
        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment): JsonResource
    {
        $attributes = $request->validated('data.attributes');

        $comment->fill($attributes);

        $post = $request->input('data.relationships.post.data.id');
        $writer = $request->input('data.relationships.writer.data.id');

        $comment->post()
            ->associate($post);

        $comment->writer()
            ->associate($writer);

        $comment->save();

        return new CommentResource($comment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): JsonResource
    {
        $comment->delete();

        return JsonResource::make([]);
    }
}
