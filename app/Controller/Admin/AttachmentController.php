<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\AttachmentRequest;
use App\Services\AttachmentService;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class AttachmentController
 * @package App\Controller\Admin
 * @Controller()
 */
class AttachmentController extends AbstractController
{
    /**
     * @param AttachmentService $attachmentService
     * @return ResponseInterface
     * @RequestMapping(path="attachment_query", methods="get")
     */
    public function attachment_query(AttachmentService $attachmentService): ResponseInterface
    {
        //交给service处理
        return $attachmentService->attachment_query($this->request);
    }

    /**
     * @param AttachmentService $attachmentService
     * @param AttachmentRequest $request
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @RequestMapping(path="attachment_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function attachment_create(AttachmentService $attachmentService, AttachmentRequest $request): ResponseInterface
    {
        //交给service处理
        return $attachmentService->attachment_create($request);
    }

    /**
     * @param AttachmentService $attachmentService
     * @param AttachmentRequest $request
     * @return ResponseInterface
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @RequestMapping(path="attachment_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function attachment_update(AttachmentService $attachmentService, AttachmentRequest $request): ResponseInterface
    {
        //交给service处理
        return $attachmentService->attachment_update($request, $this->request->input('id', -1));
    }

    /**
     * @param AttachmentService $attachmentService
     * @param AttachmentRequest $request
     * @param int $id
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @RequestMapping(path="attachment_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function attachment_delete(AttachmentService $attachmentService, AttachmentRequest $request, Filesystem $filesystem): ResponseInterface
    {
        //交给service处理
        return $attachmentService->attachment_delete($request, $filesystem, $this->request->input('id', -1));
    }
}
