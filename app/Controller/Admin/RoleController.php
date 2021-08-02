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
use App\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="role_query", methods="get")
     */
    public function role_query(RoleService $roleService): ResponseInterface
    {
        //交给service处理
        return $roleService->role_query($this->request);
    }

    /**
     * @param RoleService $roleService
     * @param RoleRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="role_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function role_create(RoleService $roleService, RoleRequest $request): ResponseInterface
    {
        //交给service处理
        return $roleService->role_create($request);
    }

    /**
     * @param RoleService $roleService
     * @param RoleRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="role_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function role_update(RoleService $roleService, RoleRequest $request): ResponseInterface
    {
        //交给service处理
        return $roleService->role_update($request, $this->request->input('id', -1));
    }

    /**
     * @param RoleService $roleService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="role_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function role_delete(RoleService $roleService): ResponseInterface
    {
        //交给service处理
        return $roleService->role_delete($this->request->input('id', -1));
    }
}
