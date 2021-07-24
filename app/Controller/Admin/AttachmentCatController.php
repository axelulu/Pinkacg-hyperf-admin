<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\AttachmentCatRequest;
use App\Services\AttachmentCatService;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class AttachmentCatController
 * @package App\Controller\Admin
 * @Controller()
 */
class AttachmentCatController extends AbstractController
{
    /**
     * @param AttachmentCatService $attachmentCatService
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(AttachmentCatService $attachmentCatService): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->index($this->request);
    }

    /**
     * @param AttachmentCatService $attachmentCatService
     * @param AttachmentCatRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(AttachmentCatService $attachmentCatService, AttachmentCatRequest $request): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->create($request);
    }

    /**
     * @param AttachmentCatService $attachmentCatService
     * @param AttachmentCatRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(AttachmentCatService $attachmentCatService, AttachmentCatRequest $request, int $id): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->update($request, $id);
    }

    /**
     * @param AttachmentCatService $attachmentCatService
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(AttachmentCatService $attachmentCatService, int $id): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->delete($id);
    }
}
