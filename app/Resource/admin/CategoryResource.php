<?php

namespace App\Resource\admin;

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
        $cat = [
            'id' => $this->id,
            'label' => $this->label,
            'value' => $this->value,
            'son' => $this->son,
            'icon' => $this->icon,
            'status' => (bool)((int)$this->status),
            'updated_at' => str_replace(array('T', 'Z'), ' ', $this->updated_at),
        ];
        $cat_item = Category::query()->where('son', $this->id)->get();
        var_dump($cat_item);
        if (count($cat_item) > 0) {
            $cat['children'] = CategoryResource::collection($cat_item);
        }
        return $cat;
    }
}
