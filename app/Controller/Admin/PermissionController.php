<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminPermission;
use App\Request\PermissionRequest;
use App\Resource\PermissionResource;
use App\Services\PermissionService;
use Donjan\Casbin\Enforcer;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
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
     * @param PermissionService $permissionService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(PermissionService $permissionService): ResponseInterface
    {
        //交给service处理
        return $this->success($permissionService->index($this->request));
    }

    /**
     * @param PermissionRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(PermissionRequest $request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $data['method'] = json_encode($data['method']);
        $flag = (new PermissionResource(AdminPermission::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param PermissionRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(PermissionRequest $request, int $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $data['method'] = json_encode($data['method']);
        $flag = AdminPermission::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
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
        if (Enforcer::getUsersForRole((string)$id)) {
            return $this->fail([], '角色存在用户！');
        }
        // 判断是否存在用户角色
        if (AdminPermission::query()->where('id', $id)->delete()) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @return ResponseInterface
     * @RequestMapping(path="createByRole", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function createByRole(): ResponseInterface
    {
        // 验证
        $data = $this->request->all();
        $flag = (new PermissionResource(AdminPermission::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="updateByRole/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateByRole(int $id): ResponseInterface
    {
        // 验证
        $data = $this->request->all();
        $data['method'] = json_encode($data['method']);
        $flag = AdminPermission::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

}
