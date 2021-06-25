<?php


namespace App\Filters;


class PermissionFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'name' => ['like', ''],
        'status' => ['like', ''],
        'p_id' => ['like', ''],
        'is_menu' => ['like', '']
    ];
}