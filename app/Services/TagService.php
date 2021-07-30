<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\TagFilter;
use App\Model\Tag;
use App\Resource\admin\TagResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class TagService extends Service
{
    /**
     * @Inject
     * @var TagFilter
     */
    protected $tagFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 12;
        $pageNo = $request->query('pageNo') ?? 1;

        //获取数据
        try {
            $tag = Tag::query()
                ->where($this->tagFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
            $tags = $tag->toArray();
            $data = self::getDisplayColumnData(TagResource::collection($tag)->toArray(), $request);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        return $this->success([
            'pageSize' => $tags['per_page'],
            'pageNo' => $tags['current_page'],
            'totalCount' => $tags['total'],
            'totalPage' => $tags['to'],
            'data' => $data,
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

        //创建内容
        try {
            $flag = Tag::query()->create($data);
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
            $flag = Tag::query()->where('id', $id)->update($data);
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
        //删除内容
        try {
            $flag = Tag::query()->where('id', $id)->delete();
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