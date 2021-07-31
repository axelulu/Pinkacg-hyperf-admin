<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\MenuFilter;
use App\Model\AdminPermission;
use App\Resource\admin\MenuPermissionResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class MenuPermissionService extends Service
{
    /**
     * @Inject
     * @var MenuFilter
     */
    protected $menuFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'sort');
        $pageSize = $request->query('pageSize') ?? 12;

        //获取数据
        try {
            $menu = AdminPermission::query()
                ->where($this->menuFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'pageNo');
            return $this->success(self::getDisplayColumnData(MenuPermissionResource::collection($menu), $request, $menu));
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //创建内容
        try {
            $flag = AdminPermission::query()->create($data);
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
     * @param $request
     * @param $id
     * @return ResponseInterface
     */
    public function update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //更新内容
        try {
            $flag = AdminPermission::query()->where('id', $id)->update($data);
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
    public function delete($id): ResponseInterface
    {
        try {
            //存在子菜单
            if (AdminPermission::query()->where('p_id', $id)) {
                return $this->fail([], '存在子菜单');
            }
            //有用户选择此菜单
            if (AdminPermission::query()->where('id', $id)->delete()) {
                return $this->success();
            }
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
        return $this->fail();
    }
}