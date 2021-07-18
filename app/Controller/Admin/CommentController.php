<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\CommentRequest;
use App\Services\CommentService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="index", methods="get")
     */
    public function index(CommentService $commentService): ResponseInterface
    {
        //交给service处理
        return $commentService->index($this->request);
    }

    /**
     * @param CommentService $commentService
     * @param CommentRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(CommentService $commentService, CommentRequest $request): ResponseInterface
    {
        //交给service处理
        return $commentService->create($request);
    }

    /**
     * @param CommentService $commentService
     * @param CommentRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(CommentService $commentService, CommentRequest $request, int $id): ResponseInterface
    {
        //交给service处理
        return $commentService->update($request, $id);
    }

    /**
     * @param CommentService $commentService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(CommentService $commentService, int $id): ResponseInterface
    {
        //交给service处理
        return $commentService->delete($id);
    }
}
