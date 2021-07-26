<?php

namespace App\Resource\admin;

use Hyperf\Resource\Json\JsonResource;

class OrderResource extends JsonResource
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
            'user_id' => $this->user_id,
            'post_id' => $this->post_id,
            'type' => $this->type,
            'download_key' => (int)$this->download_key,
            'credit' => (int)$this->credit,
            'updated_at' => str_replace(array('T', 'Z'), ' ', $this->updated_at),
        ];
    }
}
