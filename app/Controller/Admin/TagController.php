<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\TagRequest;
use App\Services\TagService;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="index", methods="get")
     */
    public function index(TagService $tagService): ResponseInterface
    {
        //交给service处理
        return $tagService->index($this->request);
    }

    /**
     * @param TagService $tagService
     * @param TagRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(TagService $tagService, TagRequest $request): ResponseInterface
    {
        //交给service处理
        return $tagService->create($request);
    }

    /**
     * @param TagService $tagService
     * @param TagRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(TagService $tagService, TagRequest $request, int $id): ResponseInterface
    {
        //交给service处理
        return $tagService->update($request, $id);
    }

    /**
     * @param TagService $tagService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(TagService $tagService, int $id): ResponseInterface
    {
        //交给service处理
        return $tagService->delete($id);
    }
}
