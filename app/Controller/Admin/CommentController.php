<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\CommentRequest;
use App\Services\CommentService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\JWT;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CommentController
 * @package App\Controller\Admin
 * @Controller()
 */
class CommentController extends AbstractController
{
    /**
     * @param CommentService $commentService
     * @return ResponseInterface
     * @RequestMapping(path="comment_query", methods="get")
     */
    public function comment_query(CommentService $commentService): ResponseInterface
    {
        //交给service处理
        return $commentService->comment_query($this->request);
    }

    /**
     * @param CommentService $commentService
     * @param CommentRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="comment_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function comment_create(CommentService $commentService, CommentRequest $request): ResponseInterface
    {
        //交给service处理
        return $commentService->comment_create($request);
    }

    /**
     * @param CommentService $commentService
     * @param JWT $JWT
     * @param CommentRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="comment_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function comment_update(CommentService $commentService, JWT $JWT, CommentRequest $request): ResponseInterface
    {
        //交给service处理
        return $commentService->comment_update($request, $JWT, $this->request->input('id', -1));
    }

    /**
     * @param CommentService $commentService
     * @param JWT $JWT
     * @param CommentRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="comment_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function comment_delete(CommentService $commentService, JWT $JWT, CommentRequest $request): ResponseInterface
    {
        //交给service处理
        return $commentService->comment_delete($request, $JWT, $this->request->input('id', -1));
    }
}
