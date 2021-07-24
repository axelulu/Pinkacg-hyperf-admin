<?php

declare(strict_types=1);

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Request\admin\UploadRequest;
use App\Services\UploadService;
use \League\Flysystem\Filesystem;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
/**
 * Class UploadController
 * @package App\Controller\Admin
 * @Controller()
 */
class UploadController extends AbstractController
{
    /**
     * 文件中转站（存在swap目录）
     * @param UploadService $uploadService
     * @param UploadRequest $request
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @RequestMapping(path="uploadFile", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function uploadFile(UploadService $uploadService, UploadRequest $request, Filesystem $filesystem): ResponseInterface
    {
        //交给service处理
        return $uploadService->uploadFile($request, $filesystem);
    }

    /**
     * @param UploadService $uploadService
     * @param UploadRequest $request
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @RequestMapping(path="uploadSiteMeta", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function uploadSiteMeta(UploadService $uploadService, UploadRequest $request, Filesystem $filesystem): ResponseInterface
    {
        //交给service处理
        return $uploadService->uploadSiteMeta($request, $filesystem);
    }
}
