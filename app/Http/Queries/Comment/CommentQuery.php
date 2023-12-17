<?php

declare(strict_types=1);

namespace App\Http\Queries\Comment;

use App\Http\Queries\Query;
use App\Models\Comment;

class CommentQuery extends Query
{
    /**
     * The model associated with the resource.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * The map of fields that can be requested.
     *
     * @var array<string, string>
     */
    protected $fields = [
        'content'   => 'content',
    ];

    /**
     * The map of relation fields that can be requested.
     *
     * @var array<string, string>
     */
    protected $relationFields = [
        'post'      => 'post',
        'writer'    => 'writer',
    ];

    /**
     * The map of fields that can be sorted.
     *
     * @var array<string, string>
     */
    protected $sortable = [
        'created'   => 'created_at',
        'updated'   => 'updated_at',
    ];

    /**
     * The map of relation fields that can be sorted.
     *
     * @var array<string, string>
     */
    protected $relationSortable = [
        'post.title'        => 'post.title',
        'post.writer.name'  => 'post.writer.name',
        'post.writer.email' => 'post.writer.email',
        'writer.name'       => 'writer.name',
        'writer.email'      => 'writer.email',
    ];

    /**
     * The map of fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $filterable = [
        'content' => [
            'field'     => 'content',
            'operators' => ['like', 'notLike'],
        ],
    ];

    /**
     * The map of relation fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $relationFilterable = [
        'post.title' => [
            'field'     => 'post.title',
            'operators' => ['like', 'notLike'],
        ],
        'post.writer.name' => [
            'field'     => 'post.writer.name',
            'operators' => ['like', 'notLike'],
        ],
        'post.writer.email' => [
            'field'     => 'post.writer.email',
            'operators' => ['equal', 'notEqual', 'like', 'notLike'],
        ],
        'writer.name' => [
            'field'     => 'writer.name',
            'operators' => ['like', 'notLike'],
        ],
        'writer.email' => [
            'field'     => 'writer.email',
            'operators' => ['equal', 'notEqual', 'like', 'notLike'],
        ],
    ];
}
