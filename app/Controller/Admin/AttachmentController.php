<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Attachment;
use App\Request\AttachmentRequest;
use App\Resource\AttachmentResource;
use App\Services\AttachmentService;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class AttachmentController
 * @package App\Controller\Admin
 * @Controller()
 */
class AttachmentController extends AbstractController
{
    /**
     * @param AttachmentService $attachmentService
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(AttachmentService $attachmentService): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $this->success($attachmentService->index($this->request));
    }

    /**
     * @param AttachmentRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="attachment")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(AttachmentRequest $request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = (new AttachmentResource(Attachment::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param AttachmentRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(AttachmentRequest $request, int $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = Attachment::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id): ResponseInterface
    {
        $flag = Attachment::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}
