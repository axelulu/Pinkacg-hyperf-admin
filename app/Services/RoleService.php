<?php


namespace App\Services;

use App\Exception\RequestException;
use App\Filters\RoleFilter;
use App\Model\PermissionRule;
use App\Model\Role;
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
    public function role_query($request): ResponseInterface
    {
        $pageSize = $request->query('pageSize') ?? 12;

        //获取内容
        try {
            $role = Role::query()
                ->where($this->roleFilter->apply())
                ->paginate((int)$pageSize, ['*'], 'pageNo');
            return $this->success(self::getDisplayColumnData(RoleResource::collection($role)->toArray(), $request, $role));
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function role_create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //创建角色
        try {
            $flag = Role::query()->create($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //赋予权限
        if (isset($data['rolePermission'])) {
            (new PermissionRule)->deletePermissionsForUser($flag['id']);
            foreach ($data['rolePermission'] as $k => $v) {
                (new PermissionRule)->addPermissionForUser($flag['id'], $v);
            }
        }

        //返回结果
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
    public function role_update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //赋予权限
        if (isset($data['rolePermission'])) {
            (new PermissionRule)->deletePermissionsForUser($id);
            foreach ($data['rolePermission'] as $k => $v) {
                (new PermissionRule)->addPermissionForUser($id, $v);
            }
            unset($data['rolePermission']);
        }

        //更新内容
        try {
            $flag = Role::query()->where('id', $id)->update($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function role_delete($id): ResponseInterface
    {
        // 判断是否存在用户角色
        if (count((new PermissionRule)->getUsersForRole((string)$id)) > 0) {
            return $this->fail([], '角色存在用户！');
        }

        //删除内容
        try {
            $flag = Role::query()->where('id', $id)->delete();
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}