<?php


namespace App\Services;


use App\Filters\AttachmentCatFilter;
use App\Model\AttachmentCat;
use App\Resource\AttachmentCatResource;

class AttachmentCatService extends Service
{
    /**
     * @var AttachmentCatFilter
     */
    private $attachmentCatFilter;

    //使用过滤器
    public function __construct(AttachmentCatFilter $attachmentCatFilter)
    {
        $this->attachmentCatFilter = $attachmentCatFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $attachmentCat = AttachmentCat::query()
            ->where($this->attachmentCatFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $attachmentCats = $attachmentCat->toArray();

        return [
            'pageSize' => $attachmentCats['per_page'],
            'pageNo' => $attachmentCats['current_page'],
            'totalCount' => $attachmentCats['total'],
            'totalPage' => $attachmentCats['to'],
            'data' => AttachmentCatResource::collection($attachmentCat),
        ];
    }
}