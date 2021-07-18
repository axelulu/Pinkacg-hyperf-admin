<?php


namespace App\Services;


use App\Filters\CommentFilter;
use App\Model\Comment;
use App\Resource\CommentResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class CommentService extends Service
{
    /**
     * @Inject
     * @var CommentFilter
     */
    protected $commentFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $comment = Comment::query()
            ->where($this->commentFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $comments = $comment->toArray();

        return $this->success([
            'pageSize' => $comments['per_page'],
            'pageNo' => $comments['current_page'],
            'totalCount' => $comments['total'],
            'totalPage' => $comments['to'],
            'data' => CommentResource::collection($comment),
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
        $flag = (new CommentResource(Comment::query()->create($data)))->toResponse();
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
        $flag = Comment::query()->where('id', $id)->update($data);
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
        $flag = Comment::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

}