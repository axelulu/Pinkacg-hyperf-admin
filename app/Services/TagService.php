<?php


namespace App\Services;


use App\Filters\TagFilter;
use App\Model\Tag;
use App\Resource\TagResource;
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
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $tag = Tag::query()
            ->where($this->tagFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $tags = $tag->toArray();

        return $this->success([
            'pageSize' => $tags['per_page'],
            'pageNo' => $tags['current_page'],
            'totalCount' => $tags['total'],
            'totalPage' => $tags['to'],
            'data' => TagResource::collection($tag),
        ]);
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function create($request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = (new TagResource(Tag::query()->create($data)))->toResponse();
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
        // 验证
        $data = $request->validated();
        $flag = Tag::query()->where('id', $id)->update($data);
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
        $flag = Tag::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}