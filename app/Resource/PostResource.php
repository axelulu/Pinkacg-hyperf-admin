<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;

class PostResource extends JsonResource
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
            'author' => $this->author,
            'title' => $this->title,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'type' => $this->type,
            'guid' => $this->guid,
            'comment_count' => (int) $this->comment_count,
            'status' => $this->status,
            'comment_status' => (int) $this->comment_status ? true : false,
            'menu' => json_decode($this->menu),
            'tag' => json_decode($this->tag),
            'download_status' => (int) $this->download_status ? true : false,
            'download' => json_decode($this->download),
            'music' => json_decode($this->music),
            'video' => json_decode($this->video),
            'views' => (int) $this->views,
            'header_img' => $this->header_img,
            'updated_at' => $this->updated_at,
        ];
    }
}
