<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\UserRequest;
use App\Services\UserService;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="user_nav", methods="get")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class)
     * })
     */
    public function user_nav(UserService $userService, JWT $JWT): ResponseInterface
    {
        return $userService->user_nav($JWT);
    }

    /**
     * @param UserService $userService
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_info", methods="get")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class)
     * })
     */
    public function user_info(UserService $userService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_info($JWT);
    }

    /**
     * @param UserService $userService
     * @return ResponseInterface
     * @RequestMapping(path="user_query", methods="get")
     */
    public function user_query(UserService $userService): ResponseInterface
    {
        //交给service处理
        return $userService->user_query($this->request);
    }

    /**
     * @param UserService $userService
     * @param UserRequest $userRequest
     * @return ResponseInterface
     * @RequestMapping(path="user_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_create(UserService $userService, UserRequest $userRequest): ResponseInterface
    {
        //交给service处理
        return $userService->user_create($userRequest);
    }

    /**
     * @param UserService $userService
     * @param UserRequest $userRequest
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_update(UserService $userService, UserRequest $userRequest, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_update($userRequest, $this->request->input('id', -1), $JWT);
    }

    /**
     * @param UserService $userService
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_update_avatar", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_update_avatar(UserService $userService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_update_avatar($this->request, $this->request->input('id', -1), $JWT);
    }

    /**
     * @param UserService $userService
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_update_info", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_update_info(UserService $userService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_update_info($this->request, $this->request->input('id', -1), $JWT);
    }

    /**
     * @param UserService $userService
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_update_email", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_update_email(UserService $userService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_update_email($this->request, $this->request->input('id', -1), $JWT);
    }

    /**
     * @param UserService $userService
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_send_email", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_send_email(UserService $userService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_send_email($this->request, $this->request->input('id', -1), $JWT);
    }

    /**
     * @param UserService $userService
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_update_password", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_update_password(UserService $userService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_update_password($this->request, $this->request->input('id', -1), $JWT);
    }

    /**
     * @param UserService $userService
     * @param UserRequest $userRequest
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="user_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function user_delete(UserService $userService, UserRequest $userRequest, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $userService->user_delete($userRequest, $this->request->input('id', -1), $JWT);
    }
}
