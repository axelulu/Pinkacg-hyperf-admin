<?php


namespace App\Services;


use App\Filters\PermissionFilter;
use App\Model\AdminPermission;
use App\Resource\PermissionResource;

class PermissionService extends Service
{
    /**
     * @var PermissionService
     */
    private $permissionFilter;

    //使用过滤器
    public function __construct(PermissionFilter $permissionFilter)
    {
        $this->permissionFilter = $permissionFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $permission = AdminPermission::query()
            ->where($this->permissionFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $permissions = $permission->toArray();

        return [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => PermissionResource::collection($permission),
        ];
    }
}