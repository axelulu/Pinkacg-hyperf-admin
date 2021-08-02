<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Services\AuthService;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Middleware\JWTAuthMiddleware;

/**
 * Class AuthController
 * @AutoController()
 */
class AuthController extends AbstractController
{
    /**
     * @param AuthService $authService
     * @param JWT $JWT
     * @return ResponseInterface
     */
    public function login(AuthService $authService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $authService->login($JWT);
    }

    /**
     * @param AuthService $authService
     * @return ResponseInterface
     */
    public function register(AuthService $authService): ResponseInterface
    {
        //交给service处理
        return $authService->register($this->request);
    }

    /**
     * @param AuthService $authService
     * @param JWT $JWT
     * @return ResponseInterface
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function refreshToken(AuthService $authService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $authService->refreshToken($JWT);
    }

    /**
     * @param AuthService $authService
     * @param JWT $JWT
     * @return ResponseInterface
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function logout(AuthService $authService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $authService->logout($JWT);
    }

    /**
     * @param AuthService $authService
     * @param JWT $JWT
     * @return ResponseInterface
     * @Middleware(JWTAuthMiddleware::class)
     */
    public function getData(AuthService $authService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $authService->getData($JWT);
    }
}
