<?php


namespace App\Filters;


class QuestionCatFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'status' => ['like', ''],
        'slug' => ['like', '']
    ];
}