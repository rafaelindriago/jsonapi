<?php

declare(strict_types=1);

namespace App\Http\Queries\Post;

use App\Http\Queries\Query;

class PostQuery extends Query
{
    /**
     * The map of fields that can be requested.
     *
     * @var array<string, string>
     */
    protected $fields = [
        'title'     => 'title',
        'content'   => 'content',
    ];

    /**
     * The map of relation fields that can be requested.
     *
     * @var array<string, string>
     */
    protected $relationFields = [
        'writer'    => 'writer',
    ];

    /**
     * The map of fields that can be sorted.
     *
     * @var array<string, string>
     */
    protected $sortable = [
        'title' => 'title',
    ];

    /**
     * The map of fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $filterable = [
        'title' => ['%%' => 'title'],
    ];
}
