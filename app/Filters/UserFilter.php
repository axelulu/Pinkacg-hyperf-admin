<?php


namespace App\Filters;


class UserFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'name' => ['like', ''],
        'check' => ['like', ''],
        'ip' => ['like', ''],
        'email' => ['like', ''],
        'username' => ['like', '']
    ];
}