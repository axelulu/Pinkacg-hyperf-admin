<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Comment;
use App\Request\CommentRequest;
use App\Resource\CommentResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class CommentController
 * @package App\Controller\Admin
 * @Controller()
 */
class CommentController extends AbstractController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index()
    {
        $id = $this->request->input('id', '%');
        $post_ID = $this->request->input('post_ID', '%');
        $user_id = $this->request->input('user_id', '%');
        $status = $this->request->input('status', '%');
        $pageSize = $this->request->query('pageSize') ?? 10;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $permission = Comment::query()
            ->where([
                ['id', 'like', $id],
                ['post_ID', 'like', $post_ID],
                ['user_id', 'like', $user_id],
                ['status', 'like', $status]
            ])
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $permissions = $permission->toArray();

        $data = [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => CommentResource::collection($permission),
        ];
        return $this->success($data);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(CommentRequest $request)
    {
        // 验证
        $data = $request->validated();
        $flag = (new CommentResource(Comment::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(CommentRequest $request, int $id)
    {
        // 验证
        $data = $request->validated();
        $flag = Comment::query()->where('id', $id)->update($data);
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(int $id)
    {
        return $this->success();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id)
    {
        $flag = Comment::query()->where('id', $id)->delete();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }
}
