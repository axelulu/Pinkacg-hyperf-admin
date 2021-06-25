<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Comment;
use App\Model\Post;
use App\Request\PostRequest;
use App\Resource\PostResource;
use App\Services\PostService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\PermissionMiddleware;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class PostController
 * @package App\Controller\Admin
 * @Controller()
 */
class PostController extends AbstractController
{
    /**
     * @param PostService $postService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(PostService $postService): ResponseInterface
    {
        //交给service处理
        return $this->success($postService->index($this->request));
    }

    /**
     * @param PostRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(PostRequest $request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = (new PostResource(Post::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param PostRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(PostRequest $request, int $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = Post::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(int $id): ResponseInterface
    {
        return $this->success($id);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id): ResponseInterface
    {
        //判断是否有评论
        if (Comment::query()->where('post_ID', $id)->first()) {
            return $this->fail([], '文章存在评论');
        }
        $flag = Post::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}