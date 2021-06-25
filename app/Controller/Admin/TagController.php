<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Tag;
use App\Request\TagRequest;
use App\Resource\TagResource;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class TagController
 * @package App\Controller\Admin
 * @Controller()
 */
class TagController extends AbstractController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index()
    {
        $id = $this->request->input('id', '%');
        $value = $this->request->input('value', '%');
        $status = $this->request->input('status', '%');
        $pageSize = $this->request->query('pageSize') ?? 10;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $permission = Tag::query()
            ->where([
                ['id', 'like', $id],
                ['value', 'like', $value],
                ['status', 'like', $status]
            ])
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $permissions = $permission->toArray();

        $data = [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => TagResource::collection($permission),
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
    public function create(TagRequest $request)
    {
        // 验证
        $data = $request->validated();
        $flag = (new TagResource(Tag::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param TagRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(TagRequest $request, int $id)
    {
        // 验证
        $data = $request->validated();
        $flag = Tag::query()->where('id', $id)->update($data);
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
        $flag = Tag::query()->where('id', $id)->delete();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }
}
