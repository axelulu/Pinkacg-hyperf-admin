<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\UserFilter;
use App\Model\Permission;
use App\Model\PermissionRule;
use App\Model\Role;
use App\Model\Comment;
use App\Model\Post;
use App\Model\Setting;
use App\Model\User;
use App\Resource\admin\NavResource;
use App\Resource\admin\UserResource;
use Donjan\Casbin\Enforcer;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Redis\Redis;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface;

class UserService extends Service
{
    /**
     * @Inject
     * @var UserFilter
     */
    protected $userFilter;

    public function user_query($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 12;

        $user = User::query()
            ->where($this->userFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'pageNo');
        return $this->success(self::getDisplayColumnData(UserResource::collection($user)->toArray(), $request, $user));

    }

    /**
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_info($JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        $role_meta = $user['role_meta'];
        $permission = $user['permission'];
        var_dump($permission);
        $user = User::query()->find($user['id'])->toArray();
        var_dump($user);
        $permissions = array();

        //获取数据
        try {
            if (is_array($permission)) {
                foreach ($permission as $k => $v) {
                    //每一项权限
                    $permission_item = Permission::query()->where('id', $v->value_id)->first()->toArray();
                    var_dump($permission_item);
                    $method = $permission_item['method'];
                    $methods = [
                        [
                            'action' => 'POST',
                            'describe' => '新增',
                            'defaultCheck' => false
                        ], [
                            'action' => 'GET',
                            'describe' => '查询',
                            'defaultCheck' => false
                        ], [
                            'action' => 'PUT',
                            'describe' => '修改',
                            'defaultCheck' => false
                        ], [
                            'action' => 'DELETE',
                            'describe' => '删除',
                            'defaultCheck' => false
                        ]
                    ];
                    $permission_method = array();
                    if (is_array($method)) {
                        foreach ($method as $k2 => $v2) {
                            if (in_array($methods[$k2]['action'], $method)) {
                                array_push($permission_method, $methods[$k2]['action']);
                            }
                        }
                    }
                    //重构权限数组
                    $permission_new_item['roleId'] = $role_meta->id;
                    $permission_new_item['permissionId'] = $permission_item['id'];
                    $permission_new_item['permissionName'] = $permission_item['name'];
                    $permission_new_item['actions'] = '[["action" => "add","defaultCheck" => false,"describe" => "新增"],["action" => "query","defaultCheck" => false,"describe" => "查询"],["action" => "get","defaultCheck" => false,"describe" => "详情"],["action" => "update","defaultCheck" => false,"describe" => "修改"],["action" => "delete","defaultCheck" => false,"describe" => "删除"]]';
                    $permission_new_item['actionEntitySet'] = $permission_method;
                    $permission_new_item['actionList'] = null;
                    $permission_new_item['dataAccess'] = null;
                    $permissions[$k] = $permission_new_item;
                }
            }
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
        return $this->success([
            'id' => $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'avatar' => $user['avatar'],
            'background' => $user['background'],
            'credit' => $user['credit'],
            'answertest' => $user['answertest'],
            'telephone' => $user['telephone'],
            'comment_num' => Comment::query()->where('user_id', $user['id'])->count(),
            'post_num' => Post::query()->where('author', $user['id'])->count(),
            'lastLoginIp' => $user['ip'],
            'lastLoginTime' => $user['updated_at'],
            'role' => [
                'id' => $role_meta->id,
                'name' => $role_meta->name,
                'describe' => $role_meta->description,
                'status' => $role_meta->status,
                'creatorId' => 1,
                'createTime' => $role_meta->created_at,
                'deleted' => 0,
                'permissions' => $permissions
            ]
        ]);
    }

    /**
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_nav($JWT): ResponseInterface
    {
        $user = $JWT->getParserData();
        $permission = $user['permission'];
        $permissions = array();

        //获取数据
        try {
            if (is_array($permission)) {
                foreach ($permission as $k => $v) {
                    //每一项权限
                    $permission_item = NavResource::make(Permission::query()->where(['id' => $v->value_id, 'is_menu' => 1])->orderBy('sort', 'asc')->first());
                    $permissions[$k] = $permission_item;
                }
            }
            $data = NavResource::collection(Permission::query()->where(['p_id' => 0, 'is_menu' => 1])->orderBy('sort', 'asc')->get())->toArray();

            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    array_push($permissions, $v);
                }
            }
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
        return $this->success($permissions);
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function user_create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);
        $data['ip'] = $this->request->getServerParams()['remote_addr'];

        try {
            $data['password'] = $this->passwordHash($data['password']);
            if (User::query()->where('email', $data['email'])->first()) {
                return $this->fail([], '邮箱已存在');
            }
            $user_role = $data['user_role'];
            unset($data['user_role']);

            //头像和背景文件
            $avatar = $data['avatar'];
            $background = $data['background'];

            //头像和背景图片
            $data['background'] = $data['background']['path'] . $data['background']['filename'] . '.' . $data['background']['type'];
            $data['avatar'] = $data['avatar']['path'] . $data['avatar']['filename'] . '.' . $data['avatar']['type'];

            //创建用户
            $flag = UserResource::make(User::query()->create($data));
            // 转移头像文件
            $data['avatar'] = self::transferFile($flag['id'], $avatar, 'user_attachment');
            // 转移背景文件
            $data['background'] = self::transferFile($flag['id'], $background, 'user_attachment');

            //更新用户
            $flag = User::query()->where('id', $flag['id'])->update($data);
            //赋予权限
            (new PermissionRule)->addRoleForUser($flag['id'], $user_role);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_update($request, $id, $JWT): ResponseInterface
    {
        //判断是否是JWT用户
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $id)) {
            return $this->fail([], '用户id错误');
        }

        //获取验证数据
        $data = self::getValidatedData($request);
        $data['ip'] = $this->request->getServerParams()['remote_addr'];
        //更新内容
        try {
            $data['password'] = $this->passwordHash($data['password']);

            // 转移头像文件
            $data['avatar'] = self::transferFile($id, $data['avatar'], 'user_attachment');
            // 转移背景文件
            $data['background'] = self::transferFile($id, $data['background'], 'user_attachment');

            //赋予角色
            if (!self::setUserRole($id, $data['user_role'])) {
                return $this->fail([], '赋予角色失败');
            }
            unset($data['user_role']);

            $flag = User::query()->where('id', $id)->update($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_update_avatar($request, $id, $JWT): ResponseInterface
    {
        //判断是否是JWT用户
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $id)) {
            return $this->fail([], '用户id错误');
        }

        // 转移头像文件
        $avatar = $this->request->all();
        $avatar = self::transferFile($id, $avatar['avatar'], 'user_attachment');

        // 更新用户头像
        try {
            $flag = User::query()->where('id', $id)->update([
                'avatar' => $avatar
            ]);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_update_info($request, $id, $JWT): ResponseInterface
    {
        //判断是否是JWT用户
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $id)) {
            return $this->fail([], '用户id错误');
        }

        // 更新用户信息
        $data = $this->request->inputs(['name', 'desc'], ['', '']);
        try {
            $flag = User::query()->where('id', $id)->update($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_update_email($request, $id, $JWT): ResponseInterface
    {
        //判断是否是JWT用户
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $id)) {
            return $this->fail([], '用户id错误');
        }

        // 更新用户邮件
        $data = $this->request->inputs(['myConfirm', 'email'], ['', '']);
        //初始化
        $redis = ApplicationContext::getContainer()->get(Redis::class);
        //判断验证码
        if ($data['myConfirm'] === $redis->get('confirm' . $id)) {
            //更新邮箱
            try {
                User::query()->where('id', $id)->update([
                    'email' => $data['email']
                ]);
            } catch (\Throwable $throwable) {
                throw new RequestException($throwable->getMessage(), $throwable->getCode());
            }
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_send_email($request, $id, $JWT): ResponseInterface
    {
        //判断是否是JWT用户
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $id)) {
            return $this->fail([], '用户id错误');
        }

        // 发送用户邮件
        $email = $this->request->input('email', '');
        if (!empty($email)) {
            //初始化
            $redis = ApplicationContext::getContainer()->get(Redis::class);
            //生成验证码
            while (($authnum = rand() % 10000) < 1000) ;
            //存到redis里
            $redis->set('confirm' . $id, $authnum, 60);
            try {
                if (self::sendMail('邮箱验证', (string)$authnum, $email)) {
                    return $this->success();
                }
            } catch (Exception $e) {
                throw new RequestException($e->getMessage(), $e->getCode());
            }
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_update_password($request, $id, $JWT): ResponseInterface
    {
        //判断是否是JWT用户
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $id)) {
            return $this->fail([], '用户id错误');
        }

        // 更新用户密码
        $data = $this->request->inputs(['password', 'newPassword', 'confirmPassword'], ['', '', '']);
        if (empty($data['password']) || empty($data['newPassword']) || empty($data['confirmPassword'])) {
            return $this->fail([], '密码为空');
        }

        //更新密码
        try {
            if (User::query()->where('password', $this->passwordHash($data['password'])) && ($data['newPassword'] === $data['confirmPassword'])) {
                $flag = User::query()->where('id', $id)->update([
                    'password' => $this->passwordHash($data['confirmPassword'])
                ]);
                if ($flag) {
                    return $this->success();
                }
                return $this->fail();
            }
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @param $JWT
     * @return ResponseInterface
     */
    public function user_delete($request, $id, $JWT): ResponseInterface
    {
        //判断是否是JWT用户
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $id)) {
            return $this->fail([], '用户id错误');
        }

        //删除内容
        try {
            //判断用户存在文章
            if (Post::query()->where('author', $id)->first()) {
                return $this->fail([], '用户存在文章');
            }
            //判断用户存在评论
            if (Comment::query()->where('user_id', $id)->first()) {
                return $this->fail([], '用户存在评论');
            }
            $flag = User::query()->where('id', $id)->delete();
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}