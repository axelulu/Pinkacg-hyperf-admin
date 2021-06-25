<?php


namespace App\Services;


use App\Filters\MenuFilter;
use App\Model\AdminPermission;
use App\Resource\MenuResource;

class MenuService extends Service
{
    /**
     * @var MenuFilter
     */
    private $menuFilter;

    //使用过滤器
    public function __construct(MenuFilter $menuFilter)
    {
        $this->menuFilter = $menuFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $menu = AdminPermission::query()
            ->where($this->menuFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $menus = $menu->toArray();

        return [
            'pageSize' => $menus['per_page'],
            'pageNo' => $menus['current_page'],
            'totalCount' => $menus['total'],
            'totalPage' => $menus['to'],
            'data' => MenuResource::collection($menu),
        ];
    }
}