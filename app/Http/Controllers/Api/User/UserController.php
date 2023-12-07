<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\StoreUserRequest;
use App\Http\Requests\Api\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserResourceCollection;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Stringable;

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

        $this->middleware('resource.fields:users,name,email,type')
            ->only(['index']);

        $this->middleware('resourse.sort:users,name,email')
            ->only(['index']);

        $this->middleware('resource.filter:users,name,email')
            ->only(['index']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): ResourceCollection
    {
        $users = User::query()
            ->when(
                $request->has('sort.users'),
                function (Builder $query) use ($request): void {
                    $request->string('sort.users')
                        ->explode(',')
                        ->mapInto(Stringable::class)
                        ->each(function (Stringable $sort) use ($query): void {
                            if ( ! $sort->startsWith('-')) {
                                $query->orderBy($sort, 'asc');
                            } else {
                                $query->orderBy($sort->after('-'), 'desc');
                            }
                        });
                }
            )
            ->when(
                $request->has('filter.users'),
                function (Builder $query) use ($request): void {
                    $request->collect('filter.users')
                        ->each(function (string $filter, string $field) use ($query): void {
                            $query->where($field, 'like', $filter);
                        });
                }
            )
            ->paginate(
                $request->input('page.size', 10),
                $request->string('fields.users')
                    ->whenEmpty(
                        fn(Stringable $fields) => $fields->append('*'),
                        fn(Stringable $fields) => $fields->prepend('id,')
                    )
                    ->explode(',')
                    ->toArray(),
                'page[number]',
                $request->input('page.number', 1)
            );

        return new UserResourceCollection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): JsonResource
    {
        $user = new User($request->validated('data.attributes'));

        $user->save();

        return new UserResource($user);
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
        $user->fill($request->validated('data.attributes'));

        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): JsonResource
    {
        $user->delete();

        return new JsonResource([]);
    }
}
