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

    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        //获取数据
        try {
            $attachment = Attachment::query()
                ->where($this->attachmentFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
            $attachments = $attachment->toArray();
            $data = self::getDisplayColumnData(AttachmentResource::collection($attachment)->toArray(), $request);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        return $this->success([
            'pageSize' => $attachments['per_page'],
            'pageNo' => $attachments['current_page'],
            'totalCount' => $attachments['total'],
            'totalPage' => $attachments['to'],
            'data' => $data,
        ]);
    }

    /**
     * @param $request
     * @param $filesystem
     * @return ResponseInterface
     */
    public function create($request, $filesystem): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //操作附件
        $cat_name = $data['cat'];
        try {
            $filesystem->copy('uploads/' . $data['path'] . '/' . $data['filename'] . '.' . $data['type'],
                'uploads/' . $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'] . '/' . $data['filename'] . '.' . $data['type']);
            $filesystem->delete('uploads/' . $data['path'] . '/' . $data['filename'] . '.' . $data['type']);
        } catch (FileExistsException | FileNotFoundException $e) {
            throw new RequestException($e->getMessage(), $e->getCode());
        }
        $data['path'] = $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'];

        //创建附件
        try {
            $flag = Attachment::query()->create($data);
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
    public function update($request, $filesystem, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

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
            throw new RequestException($e->getMessage(), $e->getCode());
        }
        $data['path'] = $cat_name . '/' . $data['user_id'] . '/' . $data['post_id'];

        //更新附件
        try {
            $flag = Attachment::query()->where('id', $id)->update($data);
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
    public function delete($request, $filesystem, $id): ResponseInterface
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