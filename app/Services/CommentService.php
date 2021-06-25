<?php


namespace App\Services;


use App\Filters\CommentFilter;
use App\Model\Comment;
use App\Resource\CommentResource;

class CommentService extends Service
{
    /**
     * @var CommentFilter
     */
    private $commentFilter;

    //使用过滤器
    public function __construct(CommentFilter $commentFilter)
    {
        $this->commentFilter = $commentFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $comment = Comment::query()
            ->where($this->commentFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $comments = $comment->toArray();

        return [
            'pageSize' => $comments['per_page'],
            'pageNo' => $comments['current_page'],
            'totalCount' => $comments['total'],
            'totalPage' => $comments['to'],
            'data' => CommentResource::collection($comment),
        ];
    }

}