<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Comment;
use App\Model\Post;
use App\Request\PostRequest;
use App\Resource\PostResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\PermissionMiddleware;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;

/**
 * Class PostController
 * @package App\Controller\Admin
 * @Controller()
 */
class PostController extends AbstractController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index()
    {
        $id = $this->request->input('id', '%');
        $title = $this->request->input('title', '%');
        $status = $this->request->input('status', '%');
        $type = $this->request->input('type', '%');
        $author = $this->request->input('author', '%');
        $menu = $this->request->input('menu', '%');
        $orderBy = $this->request->input('orderBy', 'id');
        $pageSize = $this->request->query('pageSize') ?? 1000;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $permission = Post::query()
            ->where([
                ['id', 'like', $id],
                ['title', 'like', $title],
                ['status', 'like', $status],
                ['type', 'like', $type],
                ['author', 'like', $author],
                ['menu', 'like', '"' . $menu . '"']
            ])
            ->orderBy($orderBy, 'desc')
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $permissions = $permission->toArray();

        $data = [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => PostResource::collection($permission),
        ];
        return $this->success($data);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(PostRequest $request)
    {
        // 验证
        $data = $request->validated();
        $data['menu'] = json_encode($data['menu']);
        $data['tag'] = json_encode($data['tag']);
        $data['video'] = json_encode($data['video']);
        $data['music'] = json_encode($data['music']);
        $data['download'] = json_encode($data['download']);
        $flag = (new PostResource(Post::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param PostRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(PostRequest $request, int $id)
    {
        // 验证
        $data = $request->validated();
        $data['menu'] = json_encode($data['menu']);
        $data['tag'] = json_encode($data['tag']);
        $data['video'] = json_encode($data['video']);
        $data['music'] = json_encode($data['music']);
        $data['download'] = json_encode($data['download']);
        $flag = Post::query()->where('id', $id)->update($data);
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(int $id)
    {
        return $this->success($id);
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id)
    {
        //判断是否有评论
        if(Comment::query()->where('post_ID', $id)->first()){
            return $this->fail([], '文章存在评论');
        }
        $flag = Post ::query()->where('id', $id)->delete();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }
}