<?php

declare(strict_types=1);

namespace App\Http\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

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
     * The map of fields that can be filtered.
     *
     * @var array<string, array>
     */
    protected $filterable = [];

    /**
     * The map of operators that can be used with filters.
     *
     * @var array<string, string>
     */
    protected static $operators = [
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

        if (method_exists($this, 'boot')) {
            call_user_func([$this, 'boot']);
        }

        $this->resolveResourceType();
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
     * Resolve the resource name from the builder or class property.
     */
    protected function resolveResourceType(): void
    {
        $this->type = $this->type ?: $this->builder->getModel()
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

            $this->builder->addSelect('id');

            foreach ($requestedFields as $requestedField) {
                if (isset($this->fields[$requestedField])) {
                    $field = $this->fields[$requestedField];

                    $this->builder->addSelect($field);
                }

                if (isset($this->relationFields[$requestedField])) {
                    $relationField = $this->relationFields[$requestedField];

                    $localKey = $this->builder->getModel()
                        ->{$relationField}()
                        ->getModel()
                        ->getKeyName();

                    $foreignKey = $this->builder->getModel()
                        ->{$relationField}()
                        ->getForeignKeyName();

                    $this->builder->with("{$relationField}:{$localKey},{$foreignKey}");
                }
            }
        }
    }

    /**
     * Get the requested sortings and apply them to the query builder.
     */
    protected function sort(): void
    {
        if ($this->request->has("sort.{$this->type}")) {
            $requestedSortings = $this->request->string("sort.{$this->type}")
                ->explode(',');

            foreach ($requestedSortings as $requestedSort) {
                [$requestedField, $mode] = mb_substr($requestedSort, 0, 1) !== '-'
                    ? [$requestedSort, 'asc']
                    : [mb_substr($requestedSort, 1), 'desc'];

                if (isset($this->sortable[$requestedField])) {
                    $field = $this->sortable[$requestedField];

                    $this->builder->orderBy($field, $mode);
                }
            }
        }
    }

    /**
     * Get the requested filters and apply them to the query builder.
     */
    protected function filter(): void
    {
        if ($this->request->has("filter.{$this->type}")) {
            $requestedFilters = $this->request->collect("filter.{$this->type}");

            foreach ($requestedFilters as $requestedField => $requestedFilter) {
                $filters = [];

                foreach ($requestedFilter as $requestedOperator => $filter) {
                    if (isset(
                        $this->filterable[$requestedField][$requestedOperator],
                        self::$operators[$requestedOperator]
                    )) {

                        $field = $this->filterable[$requestedField][$requestedOperator];
                        $operator = self::$operators[$requestedOperator];

                        $filters[] = [$field, $operator, $filter];
                    }
                }

                $this->builder->where($filters);
            }
        }
    }
}
