<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\TagRequest;
use App\Services\TagService;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class TagController
 * @package App\Controller\Admin
 * @Controller()
 */
class TagController extends AbstractController
{
    /**
     * @param TagService $tagService
     * @return ResponseInterface
     * @RequestMapping(path="tag_query", methods="get")
     */
    public function tag_query(TagService $tagService): ResponseInterface
    {
        //交给service处理
        return $tagService->tag_query($this->request);
    }

    /**
     * @param TagService $tagService
     * @param TagRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="tag_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function tag_create(TagService $tagService, TagRequest $request): ResponseInterface
    {
        //交给service处理
        return $tagService->tag_create($request);
    }

    /**
     * @param TagService $tagService
     * @param TagRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="tag_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function tag_update(TagService $tagService, TagRequest $request): ResponseInterface
    {
        //交给service处理
        return $tagService->tag_update($request, $this->request->input('id', -1));
    }

    /**
     * @param TagService $tagService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="tag_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function tag_delete(TagService $tagService): ResponseInterface
    {
        //交给service处理
        return $tagService->tag_delete($this->request->input('id', -1));
    }
}
