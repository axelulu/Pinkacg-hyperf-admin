<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class CommentResource extends JsonResource
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
            'post_ID' => $this->post_ID,
            'content' => $this->content,
            'type' => $this->type,
            'parent' => $this->parent,
            'user_id' => $this->user_id,
            'like' => $this->like,
            'status' => (int) $this->status ? true : false,
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }
}
