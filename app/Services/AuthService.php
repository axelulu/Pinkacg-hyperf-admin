<?php


namespace App\Services;

use App\Exception\RequestException;
use App\Model\AdminRole;
use App\Model\Comment;
use App\Model\Post;
use App\Model\Setting;
use App\Model\User;
use Donjan\Casbin\Enforcer;
use Hyperf\Redis\Redis;
use Hyperf\Utils\ApplicationContext;
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
            $userData = [
                'id' => $user->id,
                'username' => $username,
                'email' => $user->email,
                'name' => $user->name,
                'desc' => $user->desc,
                'credit' => $user->credit,
                'avatar' => $user->avatar,
                'answertest' => $user->answertest,
                'post_num' => Post::query()->where('id', $user->id)->count(),
                'comment_num' => Comment::query()->where('user_id', $user->id)->count(),
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
            try {
                User::query()->where('username', $username)->update([
                    'updated_at' => date('Y-m-d H:i:s'),
                    'remember_token' => $token
                ]);
            } catch (\Throwable $throwable) {
                throw new RequestException($throwable->getMessage(), $throwable->getCode());
            }

            $data = [
                'token' => (string)$token,
                'exp' => $JWT->getTTL(),
            ];
            return $this->success($data);
        }
        return $this->fail([], '登陆失败');
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function register($request): ResponseInterface
    {
        $data = $request->all();
        if (isset($data['username']) && isset($data['email']) && isset($data['password'])) {
            //创建用户
            try {
                $site_meta = \Qiniu\json_decode((Setting::query()->where('name', 'site_meta')->first()->toArray())['value']);
                $default_avatar = $site_meta->default_avatar;
                $default_background = $site_meta->default_background;
                $flag = User::query()->create([
                    'name' => $data['username'],
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'avatar' => $default_avatar,
                    'background' => $default_background,
                    'check' => 1,
                    'password' => $this->passwordHash($data['password'])
                ])->toArray();
            } catch (\Throwable $throwable) {
                throw new RequestException($throwable->getMessage(), $throwable->getCode());
            }

            if ($flag) {
                //获取角色id
                $user_role = Setting::query()->select('value')->where('name', 'site_meta')->first()->toArray();
                $user_role = \Qiniu\json_decode($user_role['value'])->register_role;
                //赋予角色
                if (!self::setUserRole($flag['id'], $user_role)) {
                    return $this->fail([], '赋予角色失败');
                }
                return $this->success([], '注册成功');
            }
        }
        return $this->fail([], '注册失败');
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