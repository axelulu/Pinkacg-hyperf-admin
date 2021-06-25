<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\AdminPermission;
use App\Model\Comment;
use App\Model\Post;
use App\Model\User;
use App\Request\UserRequest;
use App\Resource\NavResource;
use App\Resource\UserResource;
use App\Services\UserService;
use Donjan\Casbin\Enforcer;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;

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
        return $this->success($userService->nav($JWT));
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
        return $this->success($userService->info($JWT));
    }

    /**
     * @param UserService $userService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(UserService $userService): ResponseInterface
    {
        //交给service处理
        return $this->success($userService->index($this->request));
    }

    /**
     * @param UserRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(UserRequest $request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $data['password'] = $this->passwordHash($data['password']);
        $flag = (new UserResource(User::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param UserRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(UserRequest $request, int $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $data['password'] = $this->passwordHash($data['password']);
        if (!Enforcer::hasRoleForUser('roles_' . $id, $data['user_role'])) {
            Enforcer::deleteRolesForUser('roles_' . $id);
            Enforcer::addRoleForUser('roles_' . $id, $data['user_role']);
        }
        unset($data['user_role']);
        $flag = User::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(int $id): ResponseInterface
    {
        return $this->success($id);
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id): ResponseInterface
    {
        //判断用户存在文章
        if (Post::query()->where('author', $id)->first()) {
            return $this->fail([], '用户存在文章');
        }
        //判断用户存在评论
        if (Comment::query()->where('user_id', $id)->first()) {
            return $this->fail([], '用户存在评论');
        }
        $flag = User::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}
