<?php


namespace App\Filters;


class AttachmentFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'title' => ['like', ''],
        'type' => ['like', ''],
        'cat' => ['like', ''],
        'user_id' => ['like', '']
    ];
}