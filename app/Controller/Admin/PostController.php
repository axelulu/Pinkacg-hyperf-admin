<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\PostRequest;
use App\Services\PostService;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\PermissionMiddleware;
use App\Middleware\JWTAuthMiddleware;

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
     * @RequestMapping(path="post_query", methods="get")
     */
    public function post_query(PostService $postService): ResponseInterface
    {
        //交给service处理
        return $postService->post_query($this->request);
    }

    /**
     * @param PostService $postService
     * @param PostRequest $postRequest
     * @return ResponseInterface
     * @RequestMapping(path="post_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function post_create(PostService $postService, PostRequest $postRequest): ResponseInterface
    {
        //交给service处理
        return $postService->post_create($postRequest);
    }

    /**
     * @param PostService $postService
     * @param JWT $JWT
     * @param PostRequest $postRequest
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="post_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function post_update(PostService $postService, JWT $JWT, PostRequest $postRequest): ResponseInterface
    {
        //交给service处理
        return $postService->post_update($postRequest, $JWT, $this->request->input('id', -1));
    }

    /**
     * @param PostService $postService
     * @param JWT $JWT
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="post_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function post_delete(PostService $postService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $postService->post_delete($this->request, $JWT, $this->request->input('id', -1));
    }

    /**
     * @param PostService $orderService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="post_purchase", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function post_purchase(PostService $postService): ResponseInterface
    {
        //交给service处理
        return $postService->post_purchase($this->request);
    }
}