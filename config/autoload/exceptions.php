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
use Hyperf\Validation\ValidationExceptionHandler;

return [
    'handler' => [
        'http' => [
            Hyperf\HttpServer\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
            ValidationExceptionHandler::class,
            JtwExceptionHandler::class,
            // 自定义的验证异常处理器
            FromValidateExceptionHandler::class,
        ],
    ],
];
