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
     * The map of relation fields that can be sorted.
     *
     * @var array<string, string>
     */
    protected $relationSortable = [
        'writer.name'   => 'writer.name',
        'writer.email'  => 'writer.email',
    ];

    /**
     * The map of fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $filterable = [
        'title' => [
            'field'     => 'title',
            'operators' => ['like', 'notLike'],
        ],
        'published' => [
            'field'     => 'published_at',
            'operators' => ['null', 'notNull', 'afterOrEqual', 'beforeOrEqual'],
        ],
    ];

    /**
     * The map of relation fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $relationFilterable = [
        'writer.name' => [
            'field'     => 'writer.name',
            'operators' => ['like', 'notLike'],
        ],
        'writer.email' => [
            'field'     => 'writer.email',
            'operators' => ['like', 'notLike'],
        ],
    ];
}
