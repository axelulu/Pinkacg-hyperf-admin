<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\User;
use Hyperf\HttpServer\Annotation\AutoController;
use Phper666\JWTAuth\JWT;
use Hyperf\HttpServer\Annotation\Middleware;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;

/**
 * Class AuthController
 * @AutoController()
 * @package App\Controller\Admin
 */
class AuthController extends AbstractController
{
    /**
     * @param JWT $JWT
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login(JWT $JWT)
    {
        $username = $this->request->input('username');
        $password = $this->request->input('password');
        $user = User::query()->where('username', $username)->first();
        if ($username && $password && $this->passwordHash($password) === $user->password) {

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
            ];

            $token = $JWT->getToken($userData);

            //更新用户登录时间
            User::query()->where('username', $username)->update([
                'updated_at' => date('Y-m-d H:i:s'),
                'remember_token' => $token
            ]);

            $data = [
                'token' => (string) $token,
                'exp' => $JWT->getTTL(),
            ];
            return $this->success($data);
        }
        return $this->fail([],'登陆失败');
    }

    /**
     * @param JWT $JWT
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function refreshToken(JWT $JWT)
    {
        $token = $JWT->refreshToken();
        $data = [
            'token' => (string) $token,
            'exp' => $JWT->getTTL(),
        ];
        return $this->success($data);
    }

    /**
     * @param JWT $JWT
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function logout(JWT $JWT)
    {
        if ($JWT->logout()) {
            return $this->success();
        };
        return $this->fail();
    }

    /**
     * @param JWT $JWT
     * @return \Psr\Http\Message\ResponseInterface
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function getData(JWT $JWT)
    {
        $data = [
            'cache_time' => $JWT->getTokenDynamicCacheTime(), // 获取token的有效时间，动态的
        ];
        return $this->success($data);
    }
}
