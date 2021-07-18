<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\MenuPermissionRequest;
use App\Services\MenuPermissionService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MenuPermissionController
 * @package App\Controller\Admin
 * @Controller()
 */
class MenuPermissionController extends AbstractController
{
    /**
     * @param MenuPermissionService $menuService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(MenuPermissionService $menuService): ResponseInterface
    {
        //交给service处理
        return $menuService->index($this->request);
    }

    /**
     * @param MenuPermissionService $menuService
     * @param MenuPermissionRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(MenuPermissionService $menuService, MenuPermissionRequest $request): ResponseInterface
    {
        //交给service处理
        return $menuService->create($request);
    }

    /**
     * @param MenuPermissionService $menuService
     * @param MenuPermissionRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(MenuPermissionService $menuService, MenuPermissionRequest $request, int $id): ResponseInterface
    {
        //交给service处理
        return $menuService->update($request, $id);
    }

    /**
     * @param MenuPermissionService $menuService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(MenuPermissionService $menuService, int $id): ResponseInterface
    {
        //交给service处理
        return $menuService->delete($id);
    }
}
