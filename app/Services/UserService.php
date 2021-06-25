<?php


namespace App\Services;


use App\Filters\UserFilter;
use App\Model\AdminPermission;
use App\Model\AdminRole;
use App\Model\User;
use App\Resource\NavResource;
use App\Resource\UserResource;
use Donjan\Casbin\Enforcer;

class UserService extends Service
{
    /**
     * @var UserFilter
     */
    private $userFilter;

    //使用过滤器
    public function __construct(UserFilter $userFilter)
    {
        $this->userFilter = $userFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $user = User::query()
            ->where($this->userFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $users = $user->toArray();

        return [
            'pageSize' => $users['per_page'],
            'pageNo' => $users['current_page'],
            'totalCount' => $users['total'],
            'totalPage' => $users['to'],
            'data' => UserResource::collection($user),
        ];
    }

    public function info($JWT): array
    {
        $user = $JWT->getParserData();
        $role_id = Enforcer::getRolesForUser('roles_' . $user['id'])[0];
        $role_meta = AdminRole::query()->where('id', $role_id)->first();
        $permission = Enforcer::getPermissionsForUser('permission_' . $role_id);
        $permissions = array();
        foreach ($permission as $k => $v) {
            //每一项权限
            $permission_item = AdminPermission::query()->where('id', $v[3])->first();
            $method = $permission_item['method'];
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
                if (in_array($methods[$k2]['action'], $method)) {
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
        return [
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
        ];
    }

    public function nav($JWT): array
    {
        $user = $JWT->getParserData();
        $role_id = Enforcer::getRolesForUser('roles_' . $user['id'])[0];
        $permission = Enforcer::getPermissionsForUser('permission_' . $role_id);
        $permissions = array();
        foreach ($permission as $k => $v) {
            //每一项权限
            $permission_item = NavResource::make(AdminPermission::query()->where(['id'=> $v[3], 'is_menu'=> 1])->orderBy('sort', 'asc')->first());
            $permissions[$k] = $permission_item;
        }
        $data = NavResource::collection(AdminPermission::query()->where(['p_id'=> 0, 'is_menu'=> 1])->orderBy('sort', 'asc')->get());
        foreach ($data as $k => $v){
            array_push($permissions, $v);
        }
        return $permissions;
    }
}