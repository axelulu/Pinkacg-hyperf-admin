<?php


namespace App\Filters;


class QuestionFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'question' => ['like', ''],
        'category' => ['like', '']
    ];
}