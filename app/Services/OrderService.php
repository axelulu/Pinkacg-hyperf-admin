<?php


namespace App\Services;


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
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $order = Order::query()
            ->where($this->orderFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $orders = $order->toArray();

        return $this->success([
            'pageSize' => $orders['per_page'],
            'pageNo' => $orders['current_page'],
            'totalCount' => $orders['total'],
            'totalPage' => $orders['to'],
            'data' => self::getDisplayColumnData(OrderResource::collection($order)->toArray(), $request),
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

        $flag = (new OrderResource(Order::query()->create($data)))->toResponse();
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

        $flag = Order::query()->where('id', $id)->update($data);
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
        $flag = Order::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function purchase($request): ResponseInterface
    {
        $data = $request->all();

        if (isset($data['credit']) && isset($data['download_key']) && isset($data['post_id']) && isset($data['user_id'])) {
            //判断积分
            $credit = (User::query()->select('credit')->where('id', $data['user_id'])->get())[0]['credit'];
            var_dump($credit);
            if ($credit < $data['credit'] || $credit <= 0) {
                return $this->fail([], '积分不够');
            }

            $flag = Order::query()->insert([
                'user_id' => $data['user_id'],
                'post_id' => $data['post_id'],
                'type' => 'post',
                'download_key' => $data['download_key'],
                'credit' => $data['credit'],
            ]);

            //扣取积分
            $credit = $credit - $data['credit'];
            User::query()->where('id', $data['user_id'])->update([
                'credit' => $credit
            ]);
            if ($flag) {
                $download = \Qiniu\json_decode((Post::query()->select('download')->where('id', $data['post_id'])->get()->toArray())[0]['download'])[$data['download_key']];
                return $this->success(['data' => $download]);
            }
        }
        return $this->fail();
    }
}