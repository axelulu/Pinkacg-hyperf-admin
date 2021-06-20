<?php

namespace App\Resource;

use Donjan\Casbin\Enforcer;
use Hyperf\Resource\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * 指示是否应保留资源的集合键。
     *
     * @var bool
     */
    public $preserveKeys = true;

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
            'username' => $this->username,
            'avatar' => $this->avatar,
            'check' => (int) $this->check ? true : false,
            'telephone' => $this->telephone,
            'ip' => $this->ip,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user_role' => (int) (Enforcer::getRolesForUser('roles_' . $this->id)[0] ?? '')
        ];
    }
}
