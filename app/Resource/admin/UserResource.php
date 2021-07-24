<?php

namespace App\Resource\admin;

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
            'desc' => $this->desc,
            'username' => $this->username,
            'avatar' => $this->avatar,
            'background' => $this->background,
            'check' => (int) $this->check ? true : false,
            'telephone' => $this->telephone,
            'answertest' => $this->answertest,
            'ip' => $this->ip,
            'email' => $this->email,
            'created_at' => str_replace(array('T','Z'),' ',$this->created_at),
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
            'user_role' => (int) (Enforcer::getRolesForUser('roles_' . $this->id)[0] ?? '')
        ];
    }
}
