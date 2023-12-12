<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\Post\PostIdentifierResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type'  => 'users',
            'id'    => (string) $this->id,

            'attributes' => [
                'name'  => $this->whenHas('name', $this->name),
                'email' => $this->whenHas('email', $this->email),
                'type'  => $this->whenHas('type', $this->type),
            ],

            'relationships' => [
                'posts' => new PostIdentifierResourceCollection($this->whenLoaded('posts')),
            ],

            $this->mergeUnless($request->route('user'), [
                'links' => [
                    'self'  => URL::route('users.show', $this),
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
                'self'  => URL::route('users.show', $this),
            ],
        ];
    }
}
