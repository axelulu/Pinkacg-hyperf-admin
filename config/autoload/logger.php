<?php

declare(strict_types=1);

use App\Exception\Handler\LogDbHandler;
use Monolog\Handler;
use Monolog\Formatter;
use Monolog\Logger;

return [
    'default' => [
        'handlers' => [
            [
                'class' => Handler\StreamHandler::class,
                'constructor' => [
                    'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
                    'level' => Logger::INFO,
                ],
                'formatter' => [
                    'class' => Formatter\LineFormatter::class,
                    'constructor' => [
                        'format' => null,
                        'dateFormat' => null,
                        'allowInlineLineBreaks' => true,
                    ],
                ],
            ],
            [
                'class' => Handler\StreamHandler::class,
                'constructor' => [
                    'stream' => BASE_PATH . '/runtime/logs/hyperf-debug.log',
                    'level' => Logger::DEBUG,
                ],
                'formatter' => [
                    'class' => Formatter\JsonFormatter::class,
                    'constructor' => [
                        'batchMode' => Formatter\JsonFormatter::BATCH_MODE_JSON,
                        'appendNewline' => true,
                    ],
                ],
            ],
//            [
//                'class' => LogDbHandler::class,
//                'formatter' => [
//                    'class' => Monolog\Formatter\LineFormatter::class,
//                    'constructor' => [
//                        'format' => "%datetime%||%channel%||%level_name%||%message%||%context%||%extra%\n",
//                        'dateFormat' => null,
//                        'allowInlineLineBreaks' => true,
//                    ],
//                ]
//            ],
        ],
    ],
];