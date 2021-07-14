<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class AttachmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'original_name' => $this->original_name,
            'filename' => $this->filename,
            'path' => $this->path,
            'type' => $this->type,
            'cat' => $this->cat,
            'size' => (int)$this->size,
            'user_id' => (bool)((int)$this->user_id),
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }
}
