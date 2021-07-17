<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AttachmentCat;
use App\Request\AttachmentCatRequest;
use App\Resource\AttachmentCatResource;
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
        return $this->success($attachmentCatService->index($this->request));
    }

    /**
     * @param AttachmentCatRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(AttachmentCatRequest $request): \Psr\Http\Message\ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = (new AttachmentCatResource(AttachmentCat::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param AttachmentCatRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(AttachmentCatRequest $request, int $id): \Psr\Http\Message\ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = AttachmentCat::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id): \Psr\Http\Message\ResponseInterface
    {
        $flag = AttachmentCat::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}
