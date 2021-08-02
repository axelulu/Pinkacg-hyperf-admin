<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\ToolRequest;
use App\Services\ToolService;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ToolController
 * @package App\Controller\Admin
 * @Controller()
 */
class ToolController extends AbstractController
{
    /**
     * @param ToolService $toolService
     * @return ResponseInterface
     * @RequestMapping(path="getColumnList", methods="post")
     */
    public function getColumnList(ToolService $toolService): ResponseInterface
    {
        //交给service处理
        return $toolService->getColumnList($this->request->all());
    }
}
