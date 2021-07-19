<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\UserRequest;
use App\Services\UserService;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Class UserController
 * @package App\Controller\Admin
 * @Controller()
 */
class UserController extends AbstractController
{
    /**
     * @param UserService $userService
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="nav", methods="get")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class)
     * })
     */
    public function nav(UserService $userService, JWT $JWT): ResponseInterface
    {
        return $userService->nav($JWT);
    }

    /**
     * @param UserService $userService
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="info", methods="get")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class)
     * })
     */
    public function info(UserService $userService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->info($JWT);
    }

    /**
     * @param UserService $userService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(UserService $userService): ResponseInterface
    {
        //交给service处理
        return $userService->index($this->request);
    }

    /**
     * @param UserService $userService
     * @param UserRequest $userRequest
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(UserService $userService, UserRequest $userRequest): ResponseInterface
    {
        //交给service处理
        return $userService->create($userRequest);
    }

    /**
     * @param UserService $userService
     * @param UserRequest $userRequest
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(UserService $userService, UserRequest $userRequest, int $id, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->update($userRequest, $id, $JWT);
    }

    /**
     * @param UserService $userService
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="updateUserAvatar/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserAvatar(UserService $userService, int $id, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->updateUserAvatar($this->request, $id, $JWT);
    }

    /**
     * @param UserService $userService
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="updateUserInfo/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserInfo(UserService $userService, int $id, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->updateUserInfo($this->request, $id, $JWT);
    }

    /**
     * @param UserService $userService
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="updateUserEmail/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserEmail(UserService $userService, int $id, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->updateUserEmail($this->request, $id, $JWT);
    }

    /**
     * @param UserService $userService
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="sendChangeMail/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function sendUserMail(UserService $userService, int $id, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->sendUserMail($this->request, $id, $JWT);
    }

    /**
     * @param UserService $userService
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="updateUserPassword/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserPassword(UserService $userService, int $id, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->updateUserPassword($this->request, $id, $JWT);
    }

    /**
     * @param UserService $userService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(UserService $userService, UserRequest $userRequest, int $id, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->delete($userRequest, $id, $JWT);
    }
}
