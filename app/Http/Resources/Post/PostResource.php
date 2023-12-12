<?php

declare(strict_types=1);

namespace App\Http\Resources\Post;

use App\Http\Resources\User\UserIdentifierResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type'  => 'posts',
            'id'    => (string) $this->id,

            'attributes' => [
                'title'     => $this->whenHas('title', $this->title),
                'content'   => $this->whenHas('content', $this->content),
            ],

            'relationships' => [
                'writer' => $this->whenHas(
                    'writer_id',
                    new UserIdentifierResource($this->whenLoaded('writer'))
                ),
            ],

            $this->mergeUnless($request->route('post'), [
                'links' => [
                    'self'  => URL::route('posts.show', $this),
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
                'self'  => URL::route('posts.show', $this),
            ],
        ];
    }
}
