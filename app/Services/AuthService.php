<?php


namespace App\Services;

use App\Model\AdminRole;
use App\Model\User;
use Donjan\Casbin\Enforcer;
use Psr\Http\Message\ResponseInterface;

class AuthService extends Service
{
    /**
     * @param $JWT
     * @return ResponseInterface
     */
    public function login($JWT): ResponseInterface
    {
        $username = $this->request->input('username');
        $password = $this->request->input('password');
        $user = User::query()->where('username', $username)->first();
        if ($username && $password && $this->passwordHash($password) === $user->password) {
            //获取用户权限
            $role_id = Enforcer::getRolesForUser('roles_' . $user->id)[0];
            $permission = Enforcer::getPermissionsForUser('permission_' . $role_id);

            //获取角色信息
            $role_meta = AdminRole::query()->where('id', $role_id)->first()->toArray();
            var_dump($role_meta);
            var_dump($permission);
            $userData = [
                'id' => $user->id,
                'username' => $username,
                'email' => $user->email,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'telephone' => $user->telephone,
                'ip' => $user->ip,
                'created_id' => $user->created_id,
                'check' => $user->check,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'permission' => $permission,
                'role_meta' => $role_meta
            ];

            $token = $JWT->getToken($userData);

            //更新用户登录时间
            User::query()->where('username', $username)->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'remember_token' => $token
            ]);

            $data = [
                'token' => (string)$token,
                'exp' => $JWT->getTTL(),
            ];
            return $this->success($data);
        }
        return $this->fail([], '登陆失败');
    }

    /**
     * @param $JWT
     * @return ResponseInterface
     */
    public function refreshToken($JWT): ResponseInterface
    {
        $token = $JWT->refreshToken();
        $data = [
            'token' => (string)$token,
            'exp' => $JWT->getTTL(),
        ];
        return $this->success($data);
    }

    /**
     * @param $JWT
     * @return ResponseInterface
     */
    public function logout($JWT): ResponseInterface
    {
        if ($JWT->logout()) {
            return $this->success();
        };
        return $this->fail();
    }

    /**
     * @param $JWT
     * @return ResponseInterface
     */
    public function getData($JWT): ResponseInterface
    {
        $data = [
            'info' => $JWT->getParserData(),
            'cache_time' => $JWT->getTokenDynamicCacheTime(), // 获取token的有效时间，动态的
        ];
        return $this->success($data);
    }
}