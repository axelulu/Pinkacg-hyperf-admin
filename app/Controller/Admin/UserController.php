<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Mail\OrderShipped;
use App\Model\Comment;
use App\Model\Post;
use App\Model\User;
use App\Request\UserRequest;
use App\Resource\UserResource;
use App\Services\UserService;
use Donjan\Casbin\Enforcer;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\Redis\Redis;
use Hyperf\Resource\Json\JsonResource;
use Hyperf\Utils\ApplicationContext;
use HyperfExt\Mail\Mail;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Phper666\JWTAuth\JWT;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
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
        if (User::query()->where('email', $data['email'])->first()) {
            return $this->fail([], '邮箱已存在');
        }
        $flag = UserResource::make(User::query()->create($data));
        //赋予权限
        Enforcer::addRoleForUser('roles_' . $flag['id'], $data['user_role']);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param UserRequest $request
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(UserRequest $request, int $id, JWT $JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        if ($user['id'] !== $id) {
            return $this->fail([], '用户id错误');
        }
        // 验证
        $data = $request->validated();
        $data['password'] = $this->passwordHash($data['password']);
        //赋予权限
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
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="updateUserAvatar/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserAvatar(int $id, JWT $JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        if ($user['id'] !== $id) {
            return $this->fail([], '用户id错误');
        }
        $data = $this->request->inputs(['name', 'desc'], ['', '']);
        $flag = User::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @param JWT $JWT
     * @return ResponseInterface
     * @RequestMapping(path="updateUserInfo/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserInfo(int $id, JWT $JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        if ($user['id'] !== $id) {
            return $this->fail([], '用户id错误');
        }
        $data = $this->request->inputs(['name', 'desc'], ['', '']);
        $flag = User::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="updateUserEmail/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserEmail(int $id, JWT $JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        if ($user['id'] !== $id) {
            return $this->fail([], '用户id错误');
        }
        $data = $this->request->inputs(['myConfirm', 'email'], ['','']);
        //初始化
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        //判断验证码
        if ($data['myConfirm'] === $redis->get('confirm' . $id)) {
            //更新邮箱
            User::query()->where('id', $id)->update([
                'email'=> $data['email']
            ]);
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @return ResponseInterface
     * @throws Exception
     * @RequestMapping(path="sendChangeMail/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function sendUserMail(int $id, JWT $JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        if ($user['id'] !== $id) {
            return $this->fail([], '用户id错误');
        }
        $email = $this->request->input('email', '');
        if (!empty($email)) {
            //初始化
            $redis = ApplicationContext::getContainer()->get(Redis::class);
            //生成验证码
            while(($authnum=rand()%10000)<1000);
            //存到redis里
            $redis->set('confirm' . $id, $authnum, 60);
            if($this->sendMail('邮箱验证', $authnum, $email)){
                return $this->success();
            }
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="updateUserPassword/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function updateUserPassword(int $id, JWT $JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        if ($user['id'] !== $id) {
            return $this->fail([], '用户id错误');
        }
        $data = $this->request->inputs(['password', 'newPassword', 'confirmPassword'], ['', '', '']);
        if (empty($data['password']) || empty($data['newPassword']) || empty($data['confirmPassword'])) {
            return $this->fail([], '密码为空');
        }
        if (User::query()->where('password', $this->passwordHash($data['password'])) && ($data['newPassword'] === $data['confirmPassword'])) {
            $flag = User::query()->where('id', $id)->update([
                'password' => $this->passwordHash($data['confirmPassword'])
            ]);
            if ($flag) {
                return $this->success();
            }
            return $this->fail();
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
