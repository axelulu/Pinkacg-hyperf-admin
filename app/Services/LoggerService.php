<?php

declare(strict_types=1);

namespace App\Services;

use Hyperf\Di\Annotation\Inject;
use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class LoggerService
{
    /**
     * @Inject
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * LoggerService constructor.
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(LoggerFactory $loggerFactory)
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = $loggerFactory->get('log', 'default');
    }

    /**
     * method
     */
    public function method()
    {
        // Do something.
        $this->logger->info("Your log message.");
    }
}
