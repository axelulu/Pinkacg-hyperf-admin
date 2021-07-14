<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Model\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Console\Output\ConsoleOutput;

class LogDbHandler extends AbstractProcessingHandler
{
    public function write(array $record): void
    {
        // 判断是否开始日志记录
        if (!config('app_log')) {
            return;
        }
        // db驱动是，允许打印框架日志，则直接输出
        if (config('hf_log') && $record['channel'] == 'hyperf') {
            $output = new ConsoleOutput();
            $output->writeln($record['formatted']);
        }
        // 判断系统允许日志类型
        if ($record['level_name'] !== 'INFO' || $record['channel'] !== 'sql') {
            return;
        }
        $saveData = $record['context'];
        $saveData['channel'] = $record['channel'];
        $saveData['message'] = is_array($record['message']) ? json_encode($record['message']) : $record['message'];
        $saveData['level_name'] = $record['level_name'];
        // db驱动，不记录框架日志，框架启动时死循环，原因不明
        if ($saveData['channel'] == 'hyperf') {
            return;
        }
        if (isset($saveData['message']['channel']) && $saveData['message']['channel'] !== 'sql') {
            return;
        }
        // 开启新的协程处理保存，避免数据混淆问题
        go(function () use ($saveData) {
            Log::query()->create($saveData);
        });
    }
}