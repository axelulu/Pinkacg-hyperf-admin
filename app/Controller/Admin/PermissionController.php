<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\PermissionRequest;
use App\Services\PermissionService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PermissionController
 * @package App\Controller\Admin
 * @Controller()
 */
class PermissionController extends AbstractController
{
    /**
     * @param PermissionService $menuService
     * @return ResponseInterface
     * @RequestMapping(path="permission_query", methods="get")
     */
    public function permission_query(PermissionService $menuService): ResponseInterface
    {
        //交给service处理
        return $menuService->permission_query($this->request);
    }

    /**
     * @param PermissionService $menuService
     * @param PermissionRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="permission_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function permission_create(PermissionService $menuService, PermissionRequest $request): ResponseInterface
    {
        //交给service处理
        return $menuService->permission_create($request);
    }

    /**
     * @param PermissionService $menuService
     * @param PermissionRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="permission_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function permission_update(PermissionService $menuService, PermissionRequest $request): ResponseInterface
    {
        //交给service处理
        return $menuService->permission_update($request, $this->request->input('id', -1));
    }

    /**
     * @param PermissionService $menuService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="permission_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function permission_delete(PermissionService $menuService): ResponseInterface
    {
        //交给service处理
        return $menuService->permission_delete($this->request->input('id', -1));
    }
}
