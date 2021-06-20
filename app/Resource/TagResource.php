<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class TagResource extends JsonResource
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
            'label' => $this->label,
            'value' => $this->value,
            'status' => (int) $this->status ? true : false,
            'updated_at' => $this->updated_at,
        ];
    }
}
