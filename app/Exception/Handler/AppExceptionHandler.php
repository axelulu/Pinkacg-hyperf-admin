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
namespace App\Exception\Handler;

use App\Common\Log;
use App\Exception\RequestException;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\ApplicationContext;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as Response;
use Hyperf\HttpServer\Contract\RequestInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @Inject
     * @var Response
     */
    protected $response;

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * requestEntry
     * 根据异常返回信息，获取请求入口（模块-控制器-方法）
     * User：YM
     * Date：2019/12/15
     * Time：上午10:53
     * @param array $backTrace
     * @return mixed|string
     */
    public function requestEntry(array $backTrace)
    {
        $moduleName = '';
        foreach ($backTrace as $v) {
            if (isset($v['file']) && stripos($v['file'],'CoreMiddleware.php')) {
                $tmp = array_reverse(explode('\\',trim($v['class'])));
                if (substr(strtolower($tmp[0]),-10) == 'controller') {
                    $module = str_replace('controller','',strtolower($tmp[1]));
                    $class = str_replace('controller','',strtolower($tmp[0]));
                    $function = $v['function'];
                    $moduleName = $class.'-'.$function;
                    if ($module) {
                        $moduleName = $module.'-'.$moduleName;
                    }
                    break;
                }
            }
        }
        if (!$moduleName) {
            $request = ApplicationContext::getContainer()->get(RequestInterface::class);
            $uri = $request->getRequestUri();
            $moduleName = str_replace('/','-',ltrim($uri,'/'));
        }
        $moduleName = $moduleName??'pinkacg';
        return $moduleName;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        // 异常信息处理
        $throwableMsg = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile()).PHP_EOL.$throwable->getTraceAsString();

        // 获取日志name，
        if ( stripos($throwable->getFile(),'LoginAuthMiddleware.php') ) {
            $uri = $this->request->getRequestUri();
            $logName = str_replace('/','-',ltrim($uri,'/'));
        } else {
            $logName = $this->requestEntry($throwable->getTrace());
        }
        // 获取日志实例
        $logger = Log::get($logName);

        // 判断是否由业务异常类抛出的异常
        if ($throwable instanceof RequestException) {
            // 阻止异常冒泡
            $this->stopPropagation();
            // 业务逻辑错误日志处理
            $logger->warning($throwableMsg);
            return $this->response->json([
                'code' => $throwable->getCode(),
                'message' => $throwable->getMessage(),
            ]);
        }


        // 系统错误日志处理
        $logger->error($throwableMsg);
        return $response->withHeader('Server', 'Hyperf')->withStatus(500)->withBody(new SwooleStream('Internal Server Error.'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
