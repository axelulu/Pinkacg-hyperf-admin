<?php


namespace App\Services;

use App\Filters\PostFilter;
use App\Model\Post;
use App\Resource\PostResource;

class PostService extends Service
{
    /**
     * @var PostFilter
     */
    private $postFilter;

    //使用过滤器
    public function __construct(PostFilter $postFilter)
    {
        $this->postFilter = $postFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $permission = Post::query()
            ->where($this->postFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $permissions = $permission->toArray();

        return [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => PostResource::collection($permission),
        ];
    }
}