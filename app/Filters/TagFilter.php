<?php


namespace App\Filters;


class TagFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'values' => ['like', ''],
        'status' => ['like', '']
    ];
}