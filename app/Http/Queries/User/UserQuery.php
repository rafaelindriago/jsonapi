<?php

declare(strict_types=1);

namespace App\Http\Queries\User;

use App\Http\Queries\Query;
use App\Models\User;

class UserQuery extends Query
{
    /**
     * The model class associated with the resource.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * The map of fields that can be requested.
     *
     * @var array<string, string>
     */
    protected $fields = [
        'name'  => 'name',
        'email' => 'email',
        'type'  => 'type',
    ];

    /**
     * The map of fields that can be sorted.
     *
     * @var array<string, string>
     */
    protected $sortable = [
        'name'  => 'name',
        'email' => 'email',
    ];

    /**
     * The map of fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $filterable = [
        'name'  => ['%%' => 'name'],
        'email' => ['%%' => 'email'],
        'type'  => ['==' => 'type'],
    ];
}
