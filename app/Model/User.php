<?php

declare (strict_types=1);
namespace App\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;
/**
 * @property int $id 
 * @property string $name 
 * @property string $email 
 * @property string $email_verified_at 
 * @property string $password 
 * @property string $remember_token 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class User extends Model
{
    use SoftDeletes;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'username', 'avatar', 'background', 'check', 'telephone', 'ip', 'email', 'password', 'remember_token', 'credit', 'answertest', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * @param $userId
     * @return bool
     */
    public static function isAdmin($userId): bool
    {
        $permissionId = (Permission::query()->select('id')->where([['path', 'ALL'], ['url', 'ALL']])->first()->toArray())['id'];
        $roleId = (PermissionRule::query()->select('value_id')->where([['type', 'roles'], ['key_id', $userId]])->first())->value_id;
        if (PermissionRule::query()->where([['type', 'permission'], ['value_id', $permissionId], ['key_id', $roleId]])->count() > 0) {
            return true;
        } else {
            return false;
        }
    }
}