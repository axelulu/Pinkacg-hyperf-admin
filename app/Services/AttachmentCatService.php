<?php


namespace App\Services;


use App\Filters\AttachmentCatFilter;
use App\Model\AttachmentCat;
use App\Resource\AttachmentCatResource;
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
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $attachmentCat = AttachmentCat::query()
            ->where($this->attachmentCatFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $attachmentCats = $attachmentCat->toArray();

        return $this->success([
            'pageSize' => $attachmentCats['per_page'],
            'pageNo' => $attachmentCats['current_page'],
            'totalCount' => $attachmentCats['total'],
            'totalPage' => $attachmentCats['to'],
            'data' => AttachmentCatResource::collection($attachmentCat),
        ]);
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function create($request): ResponseInterface
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
     * @param $request
     * @param $id
     * @return ResponseInterface
     */
    public function update($request, $id): ResponseInterface
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
     * @param $id
     * @return ResponseInterface
     */
    public function delete($id): ResponseInterface
    {
        $flag = AttachmentCat::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}