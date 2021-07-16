<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class AttachmentPostContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'uid' => $this->title,
            'name' => $this->filename . '.' . $this->type,
            'status' => 'done',
            'url' => $this->path . $this->filename . '.' . $this->type,
            'thumbUrl' => $this->path . $this->filename . '.' . $this->type,
        ];
    }
}
