<?php

declare(strict_types=1);

namespace App\Controller\Admin;


use App\Controller\AbstractController;
use App\Request\UploadRequest;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use \League\Flysystem\Filesystem;

/**
 * Class UploadController
 * @package App\Controller\Admin
 * @Controller()
 */
class UploadController extends AbstractController
{
    /**
     * @param UploadRequest $request
     * @param \League\Flysystem\Filesystem $filesystem
     * @return array|\Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @RequestMapping(path="uploadAvatar", methods="post")
     */
    public function uploadAvatar(UploadRequest $request, Filesystem $filesystem)
    {
        $file = $request->validated();
        $avatar = $file['file'];
        $userId = $file['id'];
        if (!isset($userId)) {
            return $this->fail([], '未选择用户');
        }
        if (!$avatar->isValid()) {
            return $this->fail([], '文件错误');
        }
        //获取扩展名
        $extension = $avatar->getExtension();
        //构建图片链接
        $avatarlink = 'userAvatar/' . $userId . '/' . md5(time() . $avatar->getClientFilename()) . '.' . $extension;
        $filelink = 'uploads/' . $avatarlink;
        $stream = fopen($avatar->getRealPath(), 'r+');
        $filesystem->writeStream(
            $filelink,
            $stream
        );
        fclose($stream);
        return $this->success([
            'link' => $avatarlink,
        ], '上传成功');
    }

    /**
     * @param UploadRequest $request
     * @param \League\Flysystem\Filesystem $filesystem
     * @return array|\Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @RequestMapping(path="uploadPostImg", methods="post")
     */
    public function uploadPostImg(UploadRequest $request, Filesystem $filesystem)
    {
        $file = $request->validated();
        $postImg = $file['file'];
        $postId = $file['id'];
        if (!isset($postId)) {
            return $this->fail([], '未选择文章');
        }
        if (!$postImg->isValid()) {
            return $this->fail([], '文件错误');
        }
        //获取扩展名
        $extension = $postImg->getExtension();
        //构建图片链接
        $postImglink = 'userPost/' . $postId . '/' . md5(time() . $postImg->getClientFilename()) . '.' . $extension;
        $filelink = 'uploads/' . $postImglink;
        $stream = fopen($postImg->getRealPath(), 'r+');
        $filesystem->writeStream(
            $filelink,
            $stream
        );
        fclose($stream);
        return $this->success([
            'link' => $postImglink,
        ], '上传成功');
    }

    /**
     * @param UploadRequest $request
     * @param \League\Flysystem\Filesystem $filesystem
     * @return array|\Psr\Http\Message\ResponseInterface
     * @throws \League\Flysystem\FileExistsException
     * @RequestMapping(path="uploadSiteMeta", methods="post")
     */
    public function uploadSiteMeta(UploadRequest $request, Filesystem $filesystem)
    {
        $file = $request->validated();
        $postImg = $file['file'];
        if (!$postImg->isValid()) {
            return $this->fail([], '文件错误');
        }
        //获取扩展名
        $extension = $postImg->getExtension();
        //构建图片链接
        $postImglink = 'siteMeta/' . md5(time() . $postImg->getClientFilename()) . '.' . $extension;
        $filelink = 'uploads/' . $postImglink;
        $stream = fopen($postImg->getRealPath(), 'r+');
        $filesystem->writeStream(
            $filelink,
            $stream
        );
        fclose($stream);
        return $this->success([
            'link' => $postImglink,
        ], '上传成功');
    }
}
