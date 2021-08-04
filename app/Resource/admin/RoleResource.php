<?php

namespace App\Resource\admin;

use App\Model\PermissionRule;
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
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }

    protected function rolePermission($id): array
    {
        $rolePermission = (new PermissionRule)->getPermissionsForUser($id);
        var_dump($rolePermission);
        $permission = [];
        $i=0;
        foreach($rolePermission as $v){
            if($v['value_id']){
                $permission[$i++] = (int) $v['value_id'];
            }
        }
        return $permission;
    }
}
