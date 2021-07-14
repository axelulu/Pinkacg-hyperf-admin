<?php

declare(strict_types=1);

namespace App\Services;

use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class LoggerService
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = $loggerFactory->get('log', 'default');
    }

    public function method()
    {
        // Do something.
        $this->logger->info("Your log message.");
    }
}
