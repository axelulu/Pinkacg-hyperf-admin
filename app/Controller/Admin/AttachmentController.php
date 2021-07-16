<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Attachment;
use App\Model\AttachmentCat;
use App\Request\AttachmentRequest;
use App\Resource\AttachmentResource;
use App\Services\AttachmentService;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
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
     * @param Filesystem $filesystem
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(AttachmentRequest $request, Filesystem $filesystem): \Psr\Http\Message\ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $cat_name = $data['cat'];
        try {
            $filesystem->copy('uploads/' . $data['path'] . '/' . $data['filename'] . '.' . $data['type'],
                'uploads/' . $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'] . '/' . $data['filename'] . '.' . $data['type']);
            $filesystem->delete('uploads/' . $data['path'] . '/' . $data['filename'] . '.' . $data['type']);
        } catch (FileExistsException | FileNotFoundException $e) {
            return $this->fail([], '新建出错！');
        }
        $data['path'] = $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'];
        $flag = Attachment::query()->create($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param AttachmentRequest $request
     * @param Filesystem $filesystem
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(AttachmentRequest $request, Filesystem $filesystem, int $id): \Psr\Http\Message\ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $cat_name = $data['cat'];
        $oldData = Attachment::query()->select('cat', 'user_id', 'post_id', 'filename', 'type')->where('id', $data['id'])->first();
        // 转移文件到其他目录
        try {
            if (!$filesystem->has('uploads/' . $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'] . '/' . $data['filename'] . '.' . $data['type'])) {
                $filesystem->copy('uploads/' . $oldData['cat'] . '/' . $oldData['user_id'] . '/' . $oldData['post_id'] . '/' . $oldData['filename'] . '.' . $oldData['type'],
                    'uploads/' . $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'] . '/' . $data['filename'] . '.' . $data['type']);
                $filesystem->delete('uploads/' . $oldData['cat'] . '/' . $oldData['user_id'] . '/' . $oldData['post_id'] . '/' . $oldData['filename'] . '.' . $oldData['type']);
            }
        } catch (FileExistsException | FileNotFoundException $e) {
            return $this->fail([], '更新出错！');
        }
        $data['path'] = $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'];
        $flag = Attachment::query()->where('id', $id)->update($data);
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
    public function delete(AttachmentRequest $request, int $id, Filesystem $filesystem): \Psr\Http\Message\ResponseInterface
    {
        // 验证
        $data = $request->validated();
        try {
            $filesystem->delete('uploads/' . $data['path'] . '/' . $data['filename'] . '.' . $data['type']);
        } catch (FileNotFoundException $e) {
            return $this->fail([], '删除出错！');
        }
        $flag = Attachment::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}
