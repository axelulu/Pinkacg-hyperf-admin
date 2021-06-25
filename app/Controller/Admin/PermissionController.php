<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminPermission;
use App\Request\PermissionRequest;
use App\Resource\PermissionResource;
use Donjan\Casbin\Enforcer;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class PermissionController
 * @package App\Controller\Admin
 * @Controller()
 */
class PermissionController extends AbstractController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index()
    {
        $id = $this->request->input('id', '%');
        $name = $this->request->input('name', '%');
        $status = $this->request->input('status', '%');
        $p_id = $this->request->input('p_id', '%');
        $p_id_slug = (int) $this->request->input('p_id_slug') ? '=' : '>=';
        $menu_slug = (int) $this->request->input('menu_slug') ? '=' : '<=';
        $pageSize = $this->request->query('pageSize') ?? 1000;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $permission = AdminPermission::query()
            ->where([
                ['id', 'like', $id],
                ['name', 'like', $name],
                ['status', 'like', $status],
                ['p_id', $p_id_slug, $p_id],
                ['is_menu', $menu_slug, 0]
            ])
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $permissions = $permission->toArray();

        $data = [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => PermissionResource::collection($permission),
        ];
        return $this->success($data);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(PermissionRequest $request)
    {
        // 验证
        $data = $request->validated();
        $data['method'] = json_encode($data['method']);
        $flag = (new PermissionResource(AdminPermission::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param PermissionRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(PermissionRequest $request, int $id)
    {
        // 验证
        $data = $request->validated();
        $data['method'] = json_encode($data['method']);
        $flag = AdminPermission::query()->where('id', $id)->update($data);
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(int $id)
    {
        return $this->success();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id)
    {
        if(Enforcer::getUsersForRole((string)$id)){
            return $this->fail([], '角色存在用户！');
        }
        // 判断是否存在用户角色
        if(AdminPermission::query()->where('id', $id)->delete()){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="createByRole", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function createByRole()
    {
        // 验证
        $data = $this->request->all();
        $flag = (new PermissionResource(AdminPermission::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param PermissionRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="updateByRole/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateByRole(int $id)
    {
        // 验证
        $data = $this->request->all();
        $data['method'] = json_encode($data['method']);
        $flag = AdminPermission::query()->where('id', $id)->update($data);
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

}
