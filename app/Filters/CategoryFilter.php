<?php


namespace App\Filters;


class CategoryFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'label' => ['like', ''],
        'son' => ['like', ''],
        'status' => ['like', ''],
        'value' => ['like', '']
    ];
}