<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Queries\User\UserQuery;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserResourceCollection;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserController extends Controller
{
    /**
     * Create a new controller.
     */
    public function __construct()
    {
        $this->authorizeResource(User::class);

        $this->middleware('resource.type:users')
            ->only(['store', 'update']);

        $this->middleware('resource.id:user')
            ->only(['update']);

        $this->middleware('resource.fields:users,name,email,type')
            ->only(['index']);

        $this->middleware('resourse.sort:users,name,email')
            ->only(['index']);

        $this->middleware('resource.filter:users,name,email,type')
            ->only(['index']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): ResourceCollection
    {
        $builder = User::query();

        $users = UserQuery::make($builder)
            ->paginate();

        return UserResourceCollection::make($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResource
    {
        $attributes = $request->validated('data.attributes');

        $user = User::make($attributes);

        $user->save();

        return UserResource::make($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): JsonResource
    {
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResource
    {
        $attributes = $request->validated('data.attributes');

        $user->fill($attributes);

        $user->save();

        return UserResource::make($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResource
    {
        $user->delete();

        return JsonResource::make([]);
    }
}
