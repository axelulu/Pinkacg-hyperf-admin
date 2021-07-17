<?php


namespace App\Services;


use App\Filters\TagFilter;
use App\Model\Tag;
use App\Resource\TagResource;
use Hyperf\Resource\Json\JsonResource;

class TagService extends Service
{
    /**
     * @var TagFilter
     */
    private $tagFilter;

    //使用过滤器
    public function __construct(TagFilter $tagFilter)
    {
        $this->tagFilter = $tagFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $tag = Tag::query()
            ->where($this->tagFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $tags = $tag->toArray();

        return [
            'pageSize' => $tags['per_page'],
            'pageNo' => $tags['current_page'],
            'totalCount' => $tags['total'],
            'totalPage' => $tags['to'],
            'data' => TagResource::collection($tag),
        ];
    }

    public function create($request)
    {
        // 验证
        $data = $request->validated();
        $flag = (new TagResource(Tag::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}