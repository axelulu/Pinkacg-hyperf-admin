<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminRole;
use App\Request\RoleRequest;
use App\Resource\RoleResource;
use App\Services\RoleService;
use Donjan\Casbin\Enforcer;
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
        return $this->success($roleService->index($this->request));
    }

    /**
     * @param RoleRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(RoleRequest $request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = (new RoleResource(AdminRole::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param RoleRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(RoleRequest $request, int $id): ResponseInterface
    {
        //判断权限
        $rolePermission = $this->request->input('rolePermission');
        if (isset($rolePermission)) {
            Enforcer::deletePermissionsForUser('permission_' . $id);
            foreach ($rolePermission as $k => $v) {
                Enforcer::addPermissionForUser('permission_' . $id, '*', '*', $v);
            }
        }
        // 验证
        $data = $request->validated();
        $flag = AdminRole::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(int $id): ResponseInterface
    {
        return $this->success($id);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id): ResponseInterface
    {
        // 判断是否存在用户角色
        if (Enforcer::getUsersForRole((string)$id)) {
            return $this->fail([], '角色存在用户！');
        }
        if (AdminRole::query()->where('id', $id)->delete()) {
            return $this->success();
        }
        return $this->fail();
    }
}
