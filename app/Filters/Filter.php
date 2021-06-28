<?php

namespace App\Filters;

use Hyperf\Database\Schema\Builder;
use Hyperf\HttpServer\Contract\RequestInterface;

abstract class Filter
{
    protected $request;

    /**
     * @var Builder
     */
    protected $builder;

    protected $filters = [];

    protected $simpleFilters = [];

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;

        $this->formatSimpleFilters();
    }

    /**
     * 把 simpleFilters 中没有指定过滤类型的, 自动改为 '='
     */
    protected function formatSimpleFilters()
    {
        $t = [];
        foreach ($this->simpleFilters as $field => $op) {
            if (is_int($field)) {
                $t[$op] = 'equal';
            } else {
                $t[$field] = $op;
            }
        }
        $this->simpleFilters = $t;
    }

    /**
     * 应用过滤
     *
     * @return array
     */
    public function apply(): array
    {
        $filterResult = [];
        foreach ($this->getFilters() as $filter => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($op = $this->simpleFilters[$filter] ?? null) { // 简单过滤应用
                array_push($filterResult, [$filter, $op[0], $op[1] . $value . $op[1]]);
            }
        }
        return $filterResult;
    }

    public function getFilters(): array
    {
        return $this->request->inputs(array_merge($this->filters, array_keys($this->simpleFilters)));
    }
}