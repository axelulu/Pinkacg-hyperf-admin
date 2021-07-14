<?php


namespace App\Services;


use App\Filters\AttachmentFilter;
use App\Model\Attachment;
use App\Resource\AttachmentResource;

class AttachmentService extends Service
{
    /**
     * @var AttachmentFilter
     */
    private $attachmentFilter;

    //使用过滤器
    public function __construct(AttachmentFilter $attachmentFilter)
    {
        $this->attachmentFilter = $attachmentFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $attachment = Attachment::query()
            ->where($this->attachmentFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $attachments = $attachment->toArray();

        return [
            'pageSize' => $attachments['per_page'],
            'pageNo' => $attachments['current_page'],
            'totalCount' => $attachments['total'],
            'totalPage' => $attachments['to'],
            'data' => AttachmentResource::collection($attachment),
        ];
    }
}