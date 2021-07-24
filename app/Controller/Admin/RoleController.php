<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\RoleRequest;
use App\Services\RoleService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class RoleController
 * @package App\Controller\Admin
 * @Controller()
 */
class RoleController extends AbstractController
{
    /**
     * @param RoleService $roleService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(RoleService $roleService): ResponseInterface
    {
        //交给service处理
        return $roleService->index($this->request);
    }

    /**
     * @param RoleService $roleService
     * @param RoleRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(RoleService $roleService, RoleRequest $request): ResponseInterface
    {
        //交给service处理
        return $roleService->create($request);
    }

    /**
     * @param RoleService $roleService
     * @param RoleRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(RoleService $roleService, RoleRequest $request, int $id): ResponseInterface
    {
        //交给service处理
        return $roleService->update($request, $id);
    }

    /**
     * @param RoleService $roleService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(RoleService $roleService, int $id): ResponseInterface
    {
        //交给service处理
        return $roleService->delete($id);
    }
}
