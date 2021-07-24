<?php

namespace App\Resource\admin;

use Hyperf\Resource\Json\JsonResource;

class SettingResource extends JsonResource
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
            'value' => json_decode($this->value),
        ];
    }
}
