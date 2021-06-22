<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Category;
use App\Request\CategoryRequest;
use App\Resource\CategoryResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 * @Controller()
 */
class CategoryController extends AbstractController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index()
    {
        $id = $this->request->input('id', '%');
        $label = $this->request->input('label', '%');
        $value = $this->request->input('value', '%');
        $son_slug = (int) $this->request->input('son_slug') ? '=' : '>=';
        $son = $this->request->input('son', '%');
        $status = $this->request->input('status', 1);
        $pageSize = $this->request->query('pageSize') ?? 10;
        $pageNo = $this->request->query('pageNo') ?? 1;

        $permission = Category::query()
            ->where([
                ['id', 'like', $id],
                ['label', 'like', $label],
                ['value', 'like', $value],
                ['son', $son_slug, $son],
                ['status', 'like', $status]
            ])
            ->paginate((int) $pageSize, ['*'], 'page', (int) $pageNo);
        $permissions = $permission->toArray();

        $data = [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => CategoryResource::collection($permission),
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
    public function create(CategoryRequest $request)
    {
        // 验证
        $data = $request->validated();
        $flag = (new CategoryResource(Category::query()->create($data)))->toResponse();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(CategoryRequest $request, int $id)
    {
        // 验证
        $data = $request->validated();
        $flag = Category::query()->where('id', $id)->update($data);
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
        $category = Category::query()
            ->where('id', $id)
            ->toArray();

        $data = [
            'data' => $category,
        ];
        return $this->success($data);
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
        $flag = Category::query()->where('id', $id)->delete();
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }
}
