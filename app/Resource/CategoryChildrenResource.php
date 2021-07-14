<?php

namespace App\Resource;

use App\Model\Post;
use Hyperf\Resource\Json\JsonResource;
use App\Model\Category;

class CategoryChildrenResource extends JsonResource
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
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
            'children' => CategoryChildrenResource::collection(Category::query()->where('son', $this->id)->get()),
            'num' => Post::query()->where('menu', '"' . $this->value . '"')->count()
        ];
    }
}
