<?php


namespace App\Filters;


class RoleFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'name' => ['like', ''],
        'status' => ['like', '']
    ];
}