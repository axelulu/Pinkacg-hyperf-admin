<?php

namespace App\Filters;


class PostFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'title' => ['like', '%'],
        'status' => ['like', ''],
        'type' => ['like', ''],
        'author' => ['like', ''],
        'menu' => ['like', '%']
    ];
}