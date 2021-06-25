<?php


namespace App\Filters;


class MenuFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'name' => ['like', ''],
        'status' => ['like', ''],
        'p_id' => ['like', ''],
        'is_menu' => ['like', '']
    ];
}