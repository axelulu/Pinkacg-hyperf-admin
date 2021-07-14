<?php


namespace App\Filters;


class AttachmentCatFilter extends Filter
{
    protected $simpleFilters = [
        'id' => ['like', ''],
        'label' => ['like', ''],
        'son' => ['like', ''],
        'status' => ['like', ''],
        'value' => ['like', '']
    ];
}