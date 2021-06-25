<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminPermission;
use App\Request\MenuRequest;
use App\Resource\MenuResource;
use App\Services\MenuService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class MenuController
 * @package App\Controller\Admin
 * @Controller()
 */
class MenuController extends AbstractController
{
    /**
     * @param MenuService $service
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(MenuService $service): ResponseInterface
    {
        //交给service处理
        return $this->success($service->index($this->request));
    }

    /**
     * @param MenuRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(MenuRequest $request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $data['method'] = json_encode($data['method']);
        $flag = (new MenuResource(AdminPermission::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param MenuRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(MenuRequest $request, int $id): ResponseInterface
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
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(int $id): ResponseInterface
    {
        return $this->success();
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
        //存在子菜单
        if (AdminPermission::query()->where('p_id', $id)) {
            return $this->fail([], '存在子菜单');
        }
        //有用户选择此菜单
        if (AdminPermission::query()->where('id', $id)->delete()) {
            return $this->success();
        }
        return $this->fail();
    }
}
