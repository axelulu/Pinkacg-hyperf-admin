<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\AttachmentFilter;
use App\Model\Attachment;
use App\Resource\admin\AttachmentResource;
use Hyperf\Di\Annotation\Inject;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use Psr\Http\Message\ResponseInterface;

class AttachmentService extends Service
{
    /**
     * @Inject
     * @var AttachmentFilter
     */
    protected $attachmentFilter;

    public function attachment_query($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 12;

        //获取数据
        try {
            $attachment = Attachment::query()
                ->where($this->attachmentFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'pageNo');
            return $this->success(self::getDisplayColumnData(AttachmentResource::collection($attachment), $request, $attachment));
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function attachment_create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //更新附件
        try {
            if ($this->filesystem->has('uploads/' . $data['newFile']['path'] . $data['newFile']['filename'] . '.' . $data['newFile']['type'])) {
                $this->filesystem->copy('uploads/' . $data['newFile']['path'] . $data['newFile']['filename'] . '.' . $data['newFile']['type'],
                    'uploads/' . $data['cat'] . '/' . $data['user_id'] . '/' . $data['post_id'] . '/' . $data['newFile']['filename'] . '.' . $data['newFile']['type']);
                $this->filesystem->delete('uploads/' . $data['newFile']['path'] . $data['newFile']['filename'] . '.' . $data['newFile']['type']);
            }
            $data['newFile']['path'] = $data['cat'] . '/' . $data['user_id'] . '/' . $data['post_id'] . '/';
            $data['newFile']['cat'] = $data['cat'];
            $data['newFile']['user_id'] = $data['user_id'];
            $data['newFile']['post_id'] = $data['post_id'];
            $flag = Attachment::query()->where('id', $data['newFile']['id'])->update($data['newFile']);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function attachment_update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //获取老文件
        $oldFile = Attachment::query()->where('id', $id)->first()->toArray();
        $oldFile = 'uploads/' . $oldFile['path'] . $oldFile['filename'] . '.' . $oldFile['type'];

        try {
            //更新附件
            if (isset($data['newFile'])) {
                $newFile = 'uploads/' . $data['newFile']['path'] . $data['newFile']['filename'] . '.' . $data['newFile']['type'];
                $newFilePath = $data['cat'] . '/' . $data['user_id'] . '/' . $data['post_id'] . '/';
                if ($this->filesystem->has($newFile)) {
                    if ($this->filesystem->has($oldFile)) {
                        $this->filesystem->delete($oldFile);
                    }
                    $this->filesystem->copy($newFile, 'uploads/' . $newFilePath . $data['newFile']['filename'] . '.' . $data['newFile']['type']);
                    $this->filesystem->delete($newFile);
                }
                $data['newFile']['path'] = $newFilePath;
                $data['newFile']['cat'] = $data['cat'];
                $data['newFile']['user_id'] = $data['user_id'];
                $data['newFile']['post_id'] = $data['post_id'];
                Attachment::query()->where('id', $data['newFile']['id'])->delete();
                unset($data['newFile']['id']);
                $flag = Attachment::query()->where('id', $id)->update($data['newFile']);
            } else {
                //更新信息转移附件
                $newFile = 'uploads/' . $data['cat'] . '/' . $data['user_id'] . '/' . $data['post_id'] . '/' . $data['filename'] . '.' . $data['type'];
                if (!$this->filesystem->has($newFile)) {
                    $this->filesystem->copy($oldFile, $newFile);
                    $this->filesystem->delete($oldFile);
                    $data['path'] = $data['cat'] . '/' . $data['user_id'] . '/' . $data['post_id'] . '/';
                }
                $flag = Attachment::query()->where('id', $id)->update($data);
            }
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $filesystem
     * @param $id
     * @return ResponseInterface
     */
    public function attachment_delete($request, $filesystem, $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();

        //删除附件
        try {
            $filesystem->delete('uploads/' . $data['path'] . '/' . $data['filename'] . '.' . $data['type']);
        } catch (FileNotFoundException $e) {
            throw new RequestException($e->getMessage(), $e->getCode());
        }

        //删除数据库附件
        try {
            $flag = Attachment::query()->where('id', $id)->delete();
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}