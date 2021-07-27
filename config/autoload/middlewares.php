<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Hyperf\Validation\Middleware\ValidationMiddleware;

return [
    'http' => [
        // 数组内配置您的全局中间件，顺序根据该数组的顺序
        ValidationMiddleware::class
        // 这里隐藏了其它中间件
    ],
];
