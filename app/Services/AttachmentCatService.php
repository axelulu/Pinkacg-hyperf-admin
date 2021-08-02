<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\AttachmentCatFilter;
use App\Model\AttachmentCat;
use App\Resource\admin\AttachmentCatResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class AttachmentCatService extends Service
{
    /**
     * @Inject
     * @var AttachmentCatFilter
     */
    protected $attachmentCatFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function attachment_cat_query($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 12;

        //获取数据
        try {
            $attachmentCat = AttachmentCat::query()
                ->where($this->attachmentCatFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'pageNo');
            return $this->success(self::getDisplayColumnData(AttachmentCatResource::collection($attachmentCat), $request, $attachmentCat));
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function attachment_cat_create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //创建分类
        try {
            $flag = AttachmentCat::query()->create($data);
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
     * @param $id
     * @return ResponseInterface
     */
    public function attachment_cat_update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //更新分类
        try {
            $flag = AttachmentCat::query()->where('id', $id)->update($data);
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
     * @param $id
     * @return ResponseInterface
     */
    public function attachment_cat_delete($id): ResponseInterface
    {
        //删除分类
        try {
            $flag = AttachmentCat::query()->where('id', $id)->delete();
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