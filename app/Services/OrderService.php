<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\OrderFilter;
use App\Model\Order;
use App\Model\Post;
use App\Model\User;
use App\Resource\admin\OrderResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class OrderService extends Service
{
    /**
     * @Inject
     * @var OrderFilter
     */
    protected $orderFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function order_query($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 12;

        //获取数据
        try {
            $order = Order::query()
                ->where($this->orderFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'pageNo');
            return $this->success(self::getDisplayColumnData(OrderResource::collection($order)->toArray(), $request, $order));
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function order_create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //创建内容
        try {
            $flag = Order::query()->create($data);
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
    public function order_update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //更新内容
        try {
            $flag = Order::query()->where('id', $id)->update($data);
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
    public function order_delete($id): ResponseInterface
    {
        //删除内容
        try {
            $flag = Order::query()->where('id', $id)->delete();
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