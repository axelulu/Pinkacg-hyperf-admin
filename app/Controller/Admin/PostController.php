<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Comment;
use App\Model\Post;
use App\Request\PostRequest;
use App\Services\PostService;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\PermissionMiddleware;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;

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
     * @param PostService $postService
     * @param PostRequest $postRequest
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(PostService $postService, PostRequest $postRequest): ResponseInterface
    {
        //交给service处理
        return $postService->create($postRequest);
    }

    /**
     * @param PostService $postService
     * @param PostRequest $postRequest
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(PostService $postService, PostRequest $postRequest, int $id): ResponseInterface
    {
        //交给service处理
        return $postService->update($postRequest, $id);
    }

    /**
     * @param PostService $postService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(PostService $postService, int $id): ResponseInterface
    {
        //交给service处理
        return $postService->delete($id);
    }
}