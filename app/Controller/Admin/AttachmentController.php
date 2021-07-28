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
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="index", methods="get")
     */
    public function index(AttachmentService $attachmentService): ResponseInterface
    {
        //交给service处理
        return $attachmentService->index($this->request);
    }

    /**
     * @param AttachmentService $attachmentService
     * @param AttachmentRequest $request
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(AttachmentService $attachmentService, AttachmentRequest $request): ResponseInterface
    {
        //交给service处理
        return $attachmentService->create($request);
    }

    /**
     * @param AttachmentService $attachmentService
     * @param AttachmentRequest $request
     * @return ResponseInterface
     * @throws FileExistsException
     * @throws FileNotFoundException
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(AttachmentService $attachmentService, AttachmentRequest $request): ResponseInterface
    {
        //交给service处理
        return $attachmentService->update($request);
    }

    /**
     * @param AttachmentService $attachmentService
     * @param AttachmentRequest $request
     * @param int $id
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(AttachmentService $attachmentService, AttachmentRequest $request, int $id, Filesystem $filesystem): ResponseInterface
    {
        //交给service处理
        return $attachmentService->delete($request, $filesystem, $id);
    }
}
