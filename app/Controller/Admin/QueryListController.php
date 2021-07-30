<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\QueryListRequest;
use App\Services\QueryListService;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class QueryListController
 * @package App\Controller\Admin
 * @Controller()
 */
class QueryListController extends AbstractController
{
    /**
     * @param QueryListService $tagService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index(QueryListService $tagService): ResponseInterface
    {
        //交给service处理
        return $tagService->index($this->request);
    }

}
