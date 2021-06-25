<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class MenuChildrenResource extends JsonResource
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
            'title' => $this->title,
            'icon' => $this->icon,
            'path' => $this->path,
            'url' => $this->url,
            'status' => (int) $this->status ? true : false,
            'method' => json_decode($this->method),
            'p_id' => (int) $this->p_id,
            'is_menu' => (int) $this->is_menu ? true : false,
            'sort' => (int) $this->sort,
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }
}
