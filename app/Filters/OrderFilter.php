<?php


namespace App\Filters;


class OrderFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'user_id' => ['like', ''],
        'post_id' => ['like', ''],
        'type' => ['like', '']
    ];
}