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

use App\Exception\Handler\FromValidateExceptionHandler;
use App\Exception\Handler\JtwExceptionHandler;
use App\Exception\Handler\RequestExceptionHandler;

return [
    'handler' => [
        'http' => [
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
            JtwExceptionHandler::class,
            // 自定义的验证异常处理器
            FromValidateExceptionHandler::class,
            // 自定义请求错误异常类
            RequestExceptionHandler::class,
        ],
    ],
];
