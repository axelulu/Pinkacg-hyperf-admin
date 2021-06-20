<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminRole;
use App\Request\RoleRequest;
use App\Resource\RoleResource;
use Donjan\Casbin\Enforcer;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class RoleController
 * @package App\Controller\Admin
 * @Controller()
 * @Middlewares({
 *     @Middleware(JWTAuthMiddleware::class),
 *     @Middleware(PermissionMiddleware::class)
 * })
 */
class RoleController extends AbstractController
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
        $pageSize = $this->request->query('pageSize') ?? 10;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $role = AdminRole::query()
            ->where([
                ['id', 'like', $id],
                ['name', 'like', $name],
                ['status', 'like', $status],
            ])
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $roles = $role->toArray();

        $data = [
            'pageSize' => $roles['per_page'],
            'pageNo' => $roles['current_page'],
            'totalCount' => $roles['total'],
            'totalPage' => $roles['to'],
            'data' => RoleResource::collection($role),
        ];
        return $this->success($data);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     */
    public function create(RoleRequest $request)
    {
        // 验证
        $data = $request->validated();
        $flag = (new RoleResource(AdminRole::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param RoleRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     */
    public function update(RoleRequest $request, int $id)
    {
        $rolePermission = $this->request->input('rolePermission');
        if($rolePermission){
            Enforcer::deletePermissionsForUser('permission_' . $id);
            foreach($rolePermission as $k => $v){
                Enforcer::addPermissionForUser('permission_' . $id, '*', '*', $v);
            }
        }
        // 验证
        $data = $request->validated();
        $flag = AdminRole::query()->where('id', $id)->update($data);
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     */
    public function edit(int $id)
    {
        return $this->success($id);
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     */
    public function delete(int $id)
    {
        if(Enforcer::getUsersForRole((string)$id)){
            return $this->fail([], '角色存在用户！');
        }
        // 判断是否存在用户角色
        if(AdminRole::query()->where('id', $id)->delete()){
            return $this->success();
        }
        return $this->fail();
    }
}
