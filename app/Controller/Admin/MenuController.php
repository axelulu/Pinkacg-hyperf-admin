<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminPermission;
use App\Request\MenuRequest;
use App\Resource\MenuResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class MenuController
 * @package App\Controller\Admin
 * @Controller()
 */
class MenuController extends AbstractController
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
        $menu_slug = $this->request->input('menu_slug', '=');
        $p_id_slug = (int) $this->request->input('p_id_slug') ? '=' : '>=';
        $pageSize = $this->request->query('pageSize') ?? 1000;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $permission = AdminPermission::query()
            ->where([
                ['id', 'like', $id],
                ['name', 'like', $name],
                ['status', 'like', $status],
                ['p_id', $p_id_slug, $p_id],
                ['is_menu', $menu_slug, 1]
            ])
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $permissions = $permission->toArray();

        $data = [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => MenuResource::collection($permission),
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
    public function create(MenuRequest $request)
    {
        // 验证
        $data = $request->validated();
        $data['method'] = json_encode($data['method']);
        $flag = (new MenuResource(AdminPermission::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param MenuRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(MenuRequest $request, int $id)
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
        //存在子菜单
        if(AdminPermission::query()->where('p_id', $id)){
            return $this->fail([], '存在子菜单');
        }
        //有用户选择此菜单
        if(AdminPermission::query()->where('id', $id)->delete()){
            return $this->success();
        }
        return $this->fail();
    }
}
