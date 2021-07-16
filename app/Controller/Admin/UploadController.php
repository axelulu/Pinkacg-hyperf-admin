<?php

declare(strict_types=1);

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Model\Attachment;
use App\Model\Setting;
use App\Request\UploadRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use League\Flysystem\FileExistsException;
use \League\Flysystem\Filesystem;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UploadController
 * @package App\Controller\Admin
 * @Controller()
 */
class UploadController extends AbstractController
{
    /**
     * 文件中转站（存在swap目录）
     * @param UploadRequest $request
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @throws FileExistsException
     * @RequestMapping(path="uploadFile", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function uploadFile(UploadRequest $request, Filesystem $filesystem): ResponseInterface
    {
        $file = $request->validated();
        $postImg = $file['file'];
        if (isset($file['id'])) {
            Attachment::query()->where('id', $file['id'])->delete();
        }
        if (!$postImg->isValid()) {
            return $this->fail([], '文件错误');
        }
        //获取扩展名
        $extension = $postImg->getExtension();
        $filename = md5(time() . $postImg->getClientFilename());
        //构建图片链接
        $postImglink = 'swap/' . $filename . '.' . $extension;
        $filelink = 'uploads/' . $postImglink;
        $stream = fopen($postImg->getRealPath(), 'r+');
        $filesystem->writeStream(
            $filelink,
            $stream
        );
        fclose($stream);
        $data = [
            'title' => $filename,
            'original_name' => $postImg->getClientFilename(),
            'filename' => $filename,
            'path' => 'swap/',
            'type' => $extension,
            'cat' => 0,
            'size' => $postImg->getSize(),
            'post_id' => 0,
            'user_id' => 0
        ];
        $data = Attachment::create($data);
        return $this->success([
            'link' => $postImglink,
            'data' => $data
        ], '上传成功');
    }

    /**
     * @param UploadRequest $request
     * @param Filesystem $filesystem
     * @return ResponseInterface
     * @throws FileExistsException
     * @RequestMapping(path="uploadSiteMeta", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function uploadSiteMeta(UploadRequest $request, Filesystem $filesystem): ResponseInterface
    {
        $file = $request->validated();
        $postImg = $file['file'];
        $userId = $file['user_id'];
        $postId = $file['post_id'];
        if (isset($file['id']) && $file['id'] !== 0) {
            Attachment::query()->where('id', $file['id'])->delete();
        }
        if (!isset($userId) || !isset($postId)) {
            return $this->fail([], '文件参数缺失');
        }
        if (!$postImg->isValid()) {
            return $this->fail([], '文件错误');
        }
        //获取扩展名
        $extension = $postImg->getExtension();
        $filename = md5(time() . $postImg->getClientFilename());
        $path = \Qiniu\json_decode((Setting::query()->where([['name', 'site_meta']])->get())[0]['value'])->system_attachment;
        //构建图片链接
        $postImglink = $path . '/' . $userId .  '/' . $postId . '/' . $filename . '.' . $extension;
        $filelink = 'uploads/' . $postImglink;
        $stream = fopen($postImg->getRealPath(), 'r+');
        $filesystem->writeStream(
            $filelink,
            $stream
        );
        fclose($stream);
        $data = [
            'title' => $filename,
            'original_name' => $postImg->getClientFilename(),
            'filename' => $filename,
            'path' => $path . '/' . $userId .  '/' . $postId . '/',
            'type' => $extension,
            'cat' => $path,
            'size' => $postImg->getSize(),
            'post_id' => $postId,
            'user_id' => $userId
        ];
        $data = Attachment::create($data);
        return $this->success([
            'link' => $postImglink,
            'data' => $data
        ], '上传成功');
    }
}
