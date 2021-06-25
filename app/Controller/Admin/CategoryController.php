<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Category;
use App\Model\Post;
use App\Request\CategoryRequest;
use App\Resource\CategoryResource;
use App\Services\CategoryService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CategoryController
 * @package App\Controller\Admin
 * @Controller()
 */
class CategoryController extends AbstractController
{
    /**
     * @param CategoryService $categoryService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(CategoryService $categoryService): ResponseInterface
    {
        //交给service处理
        return $this->success($categoryService->index($this->request));
    }

    /**
     * @param CategoryRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(CategoryRequest $request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = (new CategoryResource(Category::query()->create($data)))->toResponse();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param CategoryRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(CategoryRequest $request, int $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = Category::query()->where('id', $id)->update($data);
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
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(int $id): ResponseInterface
    {
        //是否有子分类
        if (Category::query()->where('son', $id)->first()) {
            return $this->fail([], '存在子分类');
        }
        //分类是否有文章
        $category = (Category::query()->select('value')->where('id', $id)->first())['value'];
        if (Post::query()->where('menu', '"' . $category . '"')->first()) {
            return $this->fail([], '分类存在文章');
        }
        $flag = Category::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}
