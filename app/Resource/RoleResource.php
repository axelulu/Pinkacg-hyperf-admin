<?php

namespace App\Resource;

use Hyperf\Resource\Json\JsonResource;
use Donjan\Casbin\Enforcer;

class RoleResource extends JsonResource
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
            'description' => $this->description,
            'rolePermission' => $this::rolePermission($this->id),
            'status' => (int) $this->status ? true : false,
            'updated_at' => $this->updated_at,
        ];
    }

    protected function rolePermission($id)
    {
        $rolePermission = Enforcer::getPermissionsForUser('permission_' . $id);
        $permission = [];
        $i=0;
        foreach($rolePermission as $v){
            if($v[3]){
                $permission[$i++] = (int) $v[3];
            }
        }
        return $permission;
    }
}
