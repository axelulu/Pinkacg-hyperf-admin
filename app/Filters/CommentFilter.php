<?php


namespace App\Filters;


class CommentFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'post_ID' => ['like', ''],
        'status' => ['like', ''],
        'user_id' => ['like', '']
    ];
}