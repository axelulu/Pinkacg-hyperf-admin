<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'son' => $this->son,
            'icon' => $this->icon,
            'status' => (int) $this->status ? true : false,
            'updated_at' => $this->updated_at,
        ];
    }
}
