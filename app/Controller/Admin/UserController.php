<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminPermission;
use App\Model\AdminRole;
use App\Model\User;
use App\Request\UserRequest;
use App\Resource\UserResource;
use Donjan\Casbin\Enforcer;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Phper666\JWTAuth\JWT;

/**
 * Class UserController
 * @package App\Controller\Admin
 * @Controller()
 * @Middlewares({
 *     @Middleware(JWTAuthMiddleware::class),
 *     @Middleware(PermissionMiddleware::class)
 * })
 */
class UserController extends AbstractController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="info", methods="get")
     */
    public function info(JWT $JWT)
    {
        $user = $JWT->getParserData();
        $role_id = Enforcer::getRolesForUser('roles_' . $user['id'])[0];
        $role_meta = AdminRole::query()->where('id', $role_id)->first();
        $permission = Enforcer::getPermissionsForUser('permission_' . $role_id);
        $permissions = array();
        foreach ($permission as $k => $v) {
            //每一项权限
            $permission_item = AdminPermission::query()->where('id', $v[3])->first();
            $method = json_decode($permission_item['method']);
            $methods = [
                [
                    'action' => 'POST',
                    'describe' => '新增',
                    'defaultCheck' => false
                ], [
                    'action' => 'GET',
                    'describe' => '查询',
                    'defaultCheck' => false
                ], [
                    'action' => 'PUT',
                    'describe' => '修改',
                    'defaultCheck' => false
                ], [
                    'action' => 'DELETE',
                    'describe' => '删除',
                    'defaultCheck' => false
                ]
            ];
            $permission_method = array();
            foreach ($method as $k2 => $v2) {
                if(in_array($methods[$k2]['action'], $method)){
                    array_push($permission_method, $methods[$k2]['action']);
                }
            }
            //重构权限数组
            $permission_new_item['roleId'] = $role_meta['id'];
            $permission_new_item['permissionId'] = $permission_item['id'];
            $permission_new_item['permissionName'] = $permission_item['name'];
            $permission_new_item['actions'] = '[["action" => "add","defaultCheck" => false,"describe" => "新增"],["action" => "query","defaultCheck" => false,"describe" => "查询"],["action" => "get","defaultCheck" => false,"describe" => "详情"],["action" => "update","defaultCheck" => false,"describe" => "修改"],["action" => "delete","defaultCheck" => false,"describe" => "删除"]]';
            $permission_new_item['actionEntitySet'] = $permission_method;
            $permission_new_item['actionList'] = null;
            $permission_new_item['dataAccess'] = null;
            $permissions[$k] = $permission_new_item;
        }
        return $this->success([
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'password' => '',
            'avatar' => $user['avatar'],
            'status' => $user['status'],
            'telephone' => $user['telephone'],
            'lastLoginIp' => $user['ip'],
            'lastLoginTime' => $user['updated_at'],
            'creatorId' => $user['id'],
            'createTime' => $user['created_at'],
            'merchantCode' => 'TLif2btpzg079h15bk',
            'deleted' => 0,
            'roleId' => $user['username'],
            'role' => [
                'id' => $role_meta['id'],
                'name' => $role_meta['name'],
                'describe' => $role_meta['description'],
                'status' => $role_meta['status'],
                'creatorId' => 1,
                'createTime' => $role_meta['created_at'],
                'deleted' => 0,
                'permissions' => $permissions
            ]
        ]);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index()
    {
        $name = $this->request->input('name', '%');
        $username = $this->request->input('username', '%');
        $check = $this->request->input('check', '%');
        $id = $this->request->input('id', '%');
        $ip = $this->request->input('ip', '%');
        $email = $this->request->input('email', '%');
        $pageSize = $this->request->query('pageSize') ?? 10;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $user = User::query()
            ->where([
                ['name', 'like', $name],
                ['username', 'like', $username],
                ['check', 'like', $check],
                ['id', 'like', $id],
                ['ip', 'like', $ip],
                ['email', 'like', $email],
            ])
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $users = $user->toArray();

        $data = [
            'pageSize' => $users['per_page'],
            'pageNo' => $users['current_page'],
            'totalCount' => $users['total'],
            'totalPage' => $users['to'],
            'data' => UserResource::collection($user),
        ];
        return $this->success($data);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     */
    public function create(UserRequest $request)
    {
        // 验证
        $data = $request->validated();
        $data['password'] = $this->passwordHash($data['password']);
        $flag = (new UserResource(User::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     */
    public function update(UserRequest $request, int $id)
    {
        // 验证
        $data = $request->validated();
        $data['password'] = $this->passwordHash($data['password']);
        if(!Enforcer::hasRoleForUser('roles_' . $id, $data['user_role'])){
            Enforcer::deleteRolesForUser('roles_' . $id);
            Enforcer::addRoleForUser('roles_' . $id, $data['user_role']);
        }
        unset($data['user_role']);
        $flag = User::query()->where('id', $id)->update($data);
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
        $flag = User::query()->where('id', $id)->delete();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }
}
