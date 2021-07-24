<?php


namespace App\Services;

use App\Filters\RoleFilter;
use App\Model\AdminRole;
use App\Resource\admin\RoleResource;
use Donjan\Casbin\Enforcer;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class RoleService extends Service
{
    /**
     * @Inject
     * @var RoleFilter
     */
    protected $roleFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $role = AdminRole::query()
            ->where($this->roleFilter->apply())
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $roles = $role->toArray();

        return $this->success([
            'pageSize' => $roles['per_page'],
            'pageNo' => $roles['current_page'],
            'totalCount' => $roles['total'],
            'totalPage' => $roles['to'],
            'data' => self::getDisplayColumnData(RoleResource::collection($role)->toArray(), $request),
        ]);
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //创建角色
        $flag = AdminRole::query()->create($data);

        //赋予权限
        if (isset($data['rolePermission'])) {
            Enforcer::deletePermissionsForUser('permission_' . $flag['id']);
            foreach ($data['rolePermission'] as $k => $v) {
                Enforcer::addPermissionForUser('permission_' . $flag['id'], '*', '*', $v);
            }
        }

        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @return ResponseInterface
     */
    public function update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //赋予权限
        if (isset($data['rolePermission'])) {
            Enforcer::deletePermissionsForUser('permission_' . $id);
            foreach ($data['rolePermission'] as $k => $v) {
                Enforcer::addPermissionForUser('permission_' . $id, '*', '*', $v);
            }
            unset($data['rolePermission']);
        }

        $flag = AdminRole::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function delete($id): ResponseInterface
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