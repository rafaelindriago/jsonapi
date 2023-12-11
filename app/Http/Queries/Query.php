<?php

declare(strict_types=1);

namespace App\Http\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use InvalidArgumentException;

class Query
{
    /**
     * The builder of the query.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    protected $builder;

    /**
     * The request with the query parameters.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The resource type.
     *
     * @var string
     */
    protected $type;

    /**
     * The table associated with the resource.
     *
     * @var string
     */
    protected $table;

    /**
     * The map of fields that can be requested.
     *
     * @var array<string, string>
     */
    protected $fields = [];

    /**
     * The map of relation fields that can be requested.
     *
     * @var array<string, string>
     */
    protected $relationFields = [];

    /**
     * The map of fields that can be sorted.
     *
     * @var array<string, string>
     */
    protected $sortable = [];

    /**
     * The map of relation fields that can be sorted.
     *
     * @var array<string, string>
     */
    protected $relationSortable = [];

    /**
     * The map of fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $filterable = [];

    /**
     * The map of relation fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $relationFilterable = [];

    /**
     * The map of operators that can be used with filters.
     *
     * @var array<string, string>
     */
    protected $operators = [
        '=='    => '=',
        '!='    => '!=',
        '>>'    => '>',
        '>='    => '>=',
        '<<'    => '<',
        '<='    => '<=',
        '%%'    => 'like',
    ];

    /**
     * Create a new resource query.
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
        $this->request = Request::capture();

        $this->resolveResourceType();
        $this->resolveResourceTable();

        if (method_exists($this, 'boot')) {
            call_user_func([$this, 'boot']);
        }

        $this->sparseFields();
        $this->sort();
        $this->filter();

        if (method_exists($this, 'booted')) {
            call_user_func([$this, 'booted']);
        }
    }

    /**
     * Make a new resource query.
     */
    public static function make(Builder $builder): static
    {
        return new static($builder);
    }

    /**
     * Execute the query.
     */
    public function get(): Collection
    {
        return $this->builder->get();
    }

    /**
     * Execute the query and paginate the results.
     */
    public function paginate(): LengthAwarePaginator
    {
        $size = $this->request->input('page.size');
        $number = $this->request->input('page.number');

        return $this->builder->paginate($size, ['*'], 'page[number]', $number)
            ->appends($this->request->query());
    }

    /**
     * Execute the query and paginate the results with simple links.
     */
    public function simplePaginate(): Paginator
    {
        $size = $this->request->input('page.size');
        $number = $this->request->input('page.number');

        return $this->builder->simplePaginate($size, ['*'], 'page[number]', $number)
            ->appends($this->request->query());
    }

    /**
     * Resolve the resource type from the builder or class property.
     */
    protected function resolveResourceType(): void
    {
        $this->type = $this->type ?: $this->builder->getModel()
            ->getTable();
    }

    /**
     * Resolve the table name associated with the resource from the builder of class property.
     */
    protected function resolveResourceTable(): void
    {
        $this->table = $this->table ?: $this->builder->getModel()
            ->getTable();
    }

    /**
     * Get the requested fields and apply them to the query builder.
     */
    protected function sparseFields(): void
    {
        if ($this->request->has("fields.{$this->type}")) {
            $requestedFields = $this->request->string("fields.{$this->type}")
                ->explode(',');

            $this->builder->getQuery()
                ->addSelect("{$this->table}.id");

            foreach ($requestedFields as $requestedField) {
                if (isset($this->fields[$requestedField])) {
                    $field = $this->fields[$requestedField];

                    $this->builder->getQuery()
                        ->addSelect("{$this->table}.{$field}");
                }

                if (isset($this->relationFields[$requestedField])) {
                    $relationField = $this->relationFields[$requestedField];

                    $relation = $this->builder->getModel()
                        ->{$relationField}();

                    if ($relation instanceof HasOneOrMany) {
                        $relatedKey = $relation->getRelated()
                            ->getKeyName();

                        $foreignKey = $relation->getForeignKeyName();

                        $this->builder->with("{$relationField}:{$relatedKey},{$foreignKey}");
                    }

                    if ($relation instanceof BelongsTo) {
                        $parentKey = $relation->getParent()
                            ->getKeyName();

                        $foreignKey = $relation->getForeignKeyName();

                        $this->builder->getQuery()
                            ->addSelect("{$this->table}.{$foreignKey}");

                        $this->builder->with("{$relationField}:{$parentKey}");
                    }
                }
            }
        }

        if ($this->builder->getQuery()->columns === null) {
            $this->builder->getQuery()
                ->select("{$this->table}.*");
        }
    }

    /**
     * Get the requested sortings and apply them to the query builder.
     *
     * @throws InvalidArgumentException
     */
    protected function sort(): void
    {
        if ($this->request->has("sort")) {
            $requestedSortings = $this->request->string("sort")
                ->explode(',');

            foreach ($requestedSortings as $requestedSort) {
                [$requestedField, $mode] = mb_substr($requestedSort, 0, 1) !== '-'
                    ? [$requestedSort, 'asc']
                    : [mb_substr($requestedSort, 1), 'desc'];

                if (isset($this->sortable[$requestedField])) {
                    $field = $this->sortable[$requestedField];

                    $this->builder->getQuery()
                        ->orderBy("{$this->table}.{$field}", $mode);
                }

                if (isset($this->relationSortable[$requestedField])) {
                    $relationField = $this->relationSortable[$requestedField];

                    $relation = null;
                    $table = $this->table;
                    $joins = [];

                    foreach ($this->builder->getQuery()->joins ?? [] as $join) {
                        if ($join instanceof JoinClause) {
                            $joins[] = $join->table;
                        }
                    }

                    foreach (array_slice(explode('.', $relationField), 0, -1) as $relationLevel) {
                        $relation = $relation instanceof BelongsTo
                            ? $relation->getModel()
                                ->{$relationLevel}()

                            : $this->builder->getModel()
                                ->{$relationLevel}();

                        if ($relation instanceof BelongsTo) {
                            $parentTable = $relation->getModel()
                                ->getTable();

                            $parentKey = $relation->getModel()
                                ->getKeyName();

                            $foreignKey = $relation->getForeignKeyName();

                            if ( ! in_array($parentTable, $joins)) {
                                $this->builder->getQuery()
                                    ->join($parentTable, "{$table}.{$foreignKey}", "{$parentTable}.{$parentKey}");
                            }

                            $table = $parentTable;
                        } else {
                            throw new InvalidArgumentException('Only fields from a BelongsTo relationship can be sorted.');
                        }
                    }

                    $field = mb_substr(mb_substr($relationField, mb_strrpos($relationField, '.')), 1);

                    $this->builder->getQuery()
                        ->orderBy("{$table}.{$field}", $mode);
                }
            }
        }
    }

    /**
     * Get the requested filters and apply them to the query builder.
     */
    protected function filter(): void
    {
        if ($this->request->has('filter')) {
            $requestedFilters = $this->request->collect('filter');

            foreach ($requestedFilters as $requestedField => $requestedFilter) {
                $filters = [];

                foreach ($requestedFilter as $requestedOperator => $filter) {
                    if (isset(
                        $this->filterable[$requestedField][$requestedOperator], $this->operators[$requestedOperator]
                    )) {

                        $field = $this->filterable[$requestedField][$requestedOperator];
                        $operator = $this->operators[$requestedOperator];

                        $filters[] = ["{$this->table}.{$field}", $operator, $filter];
                    }

                    if (isset(
                        $this->relationFilterable[$requestedField][$requestedOperator], $this->operators[$requestedOperator]
                    )) {

                        $relationField = $this->relationFilterable[$requestedField][$requestedOperator];
                        $operator = $this->operators[$requestedOperator];

                        $relationPath = mb_substr($relationField, 0, mb_strrpos($relationField, '.'));
                        $field = mb_substr(mb_substr($relationField, mb_strrpos($relationField, '.')), 1);

                        $this->builder->whereRelation($relationPath, $field, $operator, $filter);
                    }
                }

                if ( ! empty($filters)) {
                    $this->builder->where($filters);
                }
            }
        }
    }
}
