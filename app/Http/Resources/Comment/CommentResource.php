<?php

declare(strict_types=1);

namespace App\Http\Resources\Comment;

use App\Http\Resources\Post\PostIdentifierResource;
use App\Http\Resources\User\UserIdentifierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type'  => 'comments',
            'id'    => (string) $this->id,

            'attributes' => [
                'content'   => $this->whenHas('content', $this->content),
            ],

            'relationships' => [
                'post'      => $this->whenHas(
                    'post_id',
                    new PostIdentifierResource($this->whenLoaded('post'))
                ),
                'writer'    => $this->whenHas(
                    'writer_id',
                    new UserIdentifierResource($this->whenLoaded('writer'))
                ),
            ],

            $this->mergeUnless($request->route()->hasParameter('post'), [
                'links' => [
                    'self'  => URL::route('comments.show', $this),
                ],
            ]),
        ];
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'links' => [
                'self'  => URL::route('comments.show', $this),
            ],
        ];
    }
}
