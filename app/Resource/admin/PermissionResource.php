<?php

namespace App\Resource\admin;

use App\Model\AdminPermission;
use Hyperf\Resource\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        if (count(AdminPermission::query()->where('p_id', $this->id)->get()) > 0){
            return [
                'id' => $this->id,
                'name' => $this->name,
                'title' => $this->title,
                'icon' => $this->icon,
                'path' => $this->path,
                'url' => $this->url,
                'status' => (int) $this->status ? true : false,
                'method' => json_decode($this->method),
                'key' => json_decode($this->key),
                'p_id' => (int) $this->p_id,
                'is_menu' => (int) $this->is_menu ? true : false,
                'sort' => (int) $this->sort,
                'children' => PermissionResource::collection(AdminPermission::query()->where('p_id', $this->id)->orderBy('sort', 'asc')->get()),
                'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
            ];
        } else {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'title' => $this->title,
                'icon' => $this->icon,
                'path' => $this->path,
                'url' => $this->url,
                'status' => (int) $this->status ? true : false,
                'method' => json_decode($this->method),
                'key' => json_decode($this->key),
                'p_id' => (int) $this->p_id,
                'is_menu' => (int) $this->is_menu ? true : false,
                'sort' => (int) $this->sort,
                'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
            ];
        }
    }
}
