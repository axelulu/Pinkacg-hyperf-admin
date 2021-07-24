<?php


namespace App\Services;


use App\Filters\CommentFilter;
use App\Model\Comment;
use App\Model\Post;
use App\Resource\admin\CommentResource;
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
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $comments = $comment->toArray();

        return $this->success([
            'pageSize' => $comments['per_page'],
            'pageNo' => $comments['current_page'],
            'totalCount' => $comments['total'],
            'totalPage' => $comments['to'],
            'data' => self::getDisplayColumnData(CommentResource::collection($comment)->toArray(), $request),
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

        $flag = (new CommentResource(Comment::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $JWT
     * @param $id
     * @return ResponseInterface
     */
    public function update($request, $JWT, $id): ResponseInterface
    {
        //判断是否是JWT用户
        $postAuthorId = (Post::query()->select('author')->where('id', $id)->get()->toArray())[0]['author'];
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $postAuthorId)) {
            return $this->fail([], '用户id错误');
        }

        //获取验证数据
        $data = self::getValidatedData($request);

        $flag = Comment::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $JWT
     * @param $id
     * @return ResponseInterface
     */
    public function delete($request, $JWT, $id): ResponseInterface
    {
        //判断是否是JWT用户
        $postAuthorId = (Post::query()->select('author')->where('id', $id)->get()->toArray())[0]['author'];
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $postAuthorId)) {
            return $this->fail([], '用户id错误');
        }

        $flag = Comment::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

}