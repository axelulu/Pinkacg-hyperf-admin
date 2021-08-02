<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\AttachmentCatRequest;
use App\Services\AttachmentCatService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AttachmentCatController
 * @package App\Controller\Admin
 * @Controller()
 */
class AttachmentCatController extends AbstractController
{
    /**
     * @param AttachmentCatService $attachmentCatService
     * @return ResponseInterface
     * @RequestMapping(path="attachment_cat_query", methods="get")
     */
    public function attachment_cat_query(AttachmentCatService $attachmentCatService): ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->attachment_cat_query($this->request);
    }

    /**
     * @param AttachmentCatService $attachmentCatService
     * @param AttachmentCatRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="attachment_cat_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function attachment_cat_create(AttachmentCatService $attachmentCatService, AttachmentCatRequest $request): ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->attachment_cat_create($request);
    }

    /**
     * @param AttachmentCatService $attachmentCatService
     * @param AttachmentCatRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="attachment_cat_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function attachment_cat_update(AttachmentCatService $attachmentCatService, AttachmentCatRequest $request): ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->attachment_cat_update($request, $this->request->input('id', -1));
    }

    /**
     * @param AttachmentCatService $attachmentCatService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="attachment_cat_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function attachment_cat_delete(AttachmentCatService $attachmentCatService): ResponseInterface
    {
        //交给service处理
        return $attachmentCatService->attachment_cat_delete($this->request->input('id', -1));
    }
}
