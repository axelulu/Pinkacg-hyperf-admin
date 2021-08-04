<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $ptype
 * @property string $v0
 * @property string $v1
 * @property string $v2
 * @property string $v3
 * @property string $v4
 * @property string $v5
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 */
class PermissionRule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permission_rules';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'key_id', 'value_id', 'type'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function getRolesForUser($user_id): string
    {
        $roles = $this->query()->select('value_id')->where([
            'key_id' => $user_id,
            'type' => 'roles'
        ])->get()->toArray();
        if (count($roles) > 0) {
            return $roles[0]['value_id'];
        } else {
            return '';
        }
    }

    public function getUsersForRole($role_id): array
    {
        return $this->query()->where([
            'value_id' => $role_id,
            'type' => 'roles'
        ])->get()->toArray();
    }

    public function hasRoleForUser($user_id, $role_id): bool
    {
        return $this->query()->where([
            'key_id' => $user_id,
            'value_id' => $role_id,
            'type' => 'roles'
        ])->get()->count() > 0;
    }

    public function deleteRolesForUser($user_id)
    {
        return $this->query()->where([
            'key_id' => $user_id,
            'type' => 'roles'
        ])->delete();
    }

    public function addRoleForUser($user_id, $role_id)
    {
        return $this->query()->create([
            'key_id' => $user_id,
            'value_id' => $role_id,
            'type' => 'roles'
        ]);
    }

    public function getPermissionsForUser($role_id): array
    {
        return $this->query()->where([
            'key_id' => $role_id,
            'type' => 'permission'
        ])->get()->toArray();
    }

    public function deletePermissionsForUser($role_id)
    {
        return $this->query()->where([
            'key_id' => $role_id,
            'type' => 'permission'
        ])->delete();
    }

    public function addPermissionForUser($role_id, $permission_id)
    {
        return $this->query()->create([
            'key_id' => $role_id,
            'value_id' => $permission_id,
            'type' => 'permission'
        ]);
    }
}