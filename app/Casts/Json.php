<?php

namespace App\Casts;

use Hyperf\Contract\CastsAttributes;

class Json implements CastsAttributes
{
    /**
     * 将取出的数据进行转换
     */
    public function get($model, $key, $value, $attributes)
    {
        return json_decode($value, true);
    }

    /**
     * 转换成将要进行存储的值
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value);
    }
}