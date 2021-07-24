<?php

namespace App\Resource\admin;

use Hyperf\Resource\Json\JsonResource;

class AttachmentCatResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'status' => (bool)$this->status,
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }
}
