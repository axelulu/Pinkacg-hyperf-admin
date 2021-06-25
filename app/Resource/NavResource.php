<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class NavResource extends JsonResource
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
            'meta' => [
                'icon' => $this->icon,
                'title' => $this->title,
                'show' => true
            ],
            'permission' => $this->p_id == "0" ? [] : [$this->id],
            'component' => $this->p_id == "0" ? 'RouteView' : $this->url,
            'parentId' => $this->p_id,
        ];
    }
}
