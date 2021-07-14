<?php

namespace App\Resource;

use App\Model\Post;
use Hyperf\Resource\Json\JsonResource;
use App\Model\Category;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if (count(Category::query()->where('son', $this->id)->get()) > 0) {
            return [
                'id' => $this->id,
                'label' => $this->label,
                'value' => $this->value,
                'son' => $this->son,
                'icon' => $this->icon,
                'status' => (bool)((int)$this->status),
                'updated_at' => str_replace(array('T', 'Z'), ' ', $this->updated_at),
                'children' => CategoryResource::collection(Category::query()->where('son', $this->id)->get()),
                'num' => Post::query()->where('menu', '"' . $this->value . '"')->count()
            ];
        } else {
            return [
                'id' => $this->id,
                'label' => $this->label,
                'value' => $this->value,
                'son' => $this->son,
                'icon' => $this->icon,
                'status' => (bool)((int)$this->status),
                'updated_at' => str_replace(array('T', 'Z'), ' ', $this->updated_at),
                'num' => Post::query()->where('menu', '"' . $this->value . '"')->count()
            ];
        }
    }
}
