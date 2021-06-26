<?php


namespace App\Services;

use App\Filters\RoleFilter;
use App\Model\AdminRole;
use App\Resource\RoleResource;

class RoleService extends Service
{
    /**
     * @var RoleFilter
     */
    private $roleFilter;

    //使用过滤器
    public function __construct(RoleFilter $roleFilter)
    {
        $this->roleFilter = $roleFilter;
    }

    public function index($request): array
    {
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $role = AdminRole::query()
            ->where($this->roleFilter->apply())
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $roles = $role->toArray();

        return [
            'pageSize' => $roles['per_page'],
            'pageNo' => $roles['current_page'],
            'totalCount' => $roles['total'],
            'totalPage' => $roles['to'],
            'data' => RoleResource::collection($role),
        ];
    }
}