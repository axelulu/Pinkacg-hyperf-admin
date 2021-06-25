<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\User;
use Hyperf\HttpServer\Annotation\AutoController;
use Phper666\JWTAuth\JWT;
use Hyperf\HttpServer\Annotation\Middleware;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class AuthController
 * @AutoController()
 * @package App\Controller\Admin
 */
class AuthController extends AbstractController
{
    /**
     * @param JWT $JWT
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function login(JWT $JWT): ResponseInterface
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
                'token' => (string)$token,
                'exp' => $JWT->getTTL(),
            ];
            return $this->success($data);
        }
        return $this->fail([], '登陆失败');
    }

    /**
     * @param JWT $JWT
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function refreshToken(JWT $JWT): ResponseInterface
    {
        $token = $JWT->refreshToken();
        $data = [
            'token' => (string)$token,
            'exp' => $JWT->getTTL(),
        ];
        return $this->success($data);
    }

    /**
     * @param JWT $JWT
     * @return ResponseInterface
     * @throws InvalidArgumentException
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function logout(JWT $JWT): ResponseInterface
    {
        if ($JWT->logout()) {
            return $this->success();
        };
        return $this->fail();
    }

    /**
     * @param JWT $JWT
     * @return ResponseInterface
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function getData(JWT $JWT): ResponseInterface
    {
        $data = [
            'cache_time' => $JWT->getTokenDynamicCacheTime(), // 获取token的有效时间，动态的
        ];
        return $this->success($data);
    }
}
