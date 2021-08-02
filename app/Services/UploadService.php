<?php


namespace App\Services;

use App\Model\Attachment;
use App\Model\Setting;
use Psr\Http\Message\ResponseInterface;

class UploadService extends Service
{
    /**
     * @param $request
     * @param $filesystem
     * @return ResponseInterface
     */
    public function upload_img($request, $filesystem): ResponseInterface
    {
        $file = $request->validated();
        $postImg = $file['file'];
        if ($postImg->getSize() / 1024 /1024 > 4) {
            return $this->fail([], '文件超过4M');
        }
        if ($postImg->getMimeType() !== 'image/jpeg' && $postImg->getMimeType() !== 'image/png') {
            return $this->fail([], '文件类型错误');
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
     * @param $request
     * @param $filesystem
     * @return ResponseInterface
     */
    public function upload_setting($request, $filesystem): ResponseInterface
    {
        $file = $request->validated();
        $postImg = $file['file'];
        $userId = $file['user_id'];
        $postId = $file['post_id'];
        if (isset($file['id']) && $file['id'] !== 0) {
            Attachment::query()->where('id', $file['id'])->delete();
        }
        if ($postImg->getSize() / 1024 /1024 > 2) {
            return $this->fail([], '文件超过2M');
        }
        if ($postImg->getMimeType() !== 'image/jpeg' && $postImg->getMimeType() !== 'image/png') {
            return $this->fail([], '文件类型错误');
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
        $path = \Qiniu\json_decode(Setting::query()->where([['name', 'site_meta']])->first()['value'])->system_attachment;
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