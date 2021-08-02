<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\CategoryRequest;
use App\Services\CategoryService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="category_query", methods="get")
     */
    public function category_query(CategoryService $categoryService): ResponseInterface
    {
        //交给service处理
        return $categoryService->category_query($this->request);
    }

    /**
     * @param CategoryService $categoryService
     * @param CategoryRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="category_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function category_create(CategoryService $categoryService, CategoryRequest $request): ResponseInterface
    {
        //交给service处理
        return $categoryService->category_create($request);
    }

    /**
     * @param CategoryService $categoryService
     * @param CategoryRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="category_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function category_update(CategoryService $categoryService, CategoryRequest $request): ResponseInterface
    {
        //交给service处理
        return $categoryService->category_update($request, $this->request->input('id', -1));
    }

    /**
     * @param CategoryService $categoryService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="category_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function category_delete(CategoryService $categoryService): ResponseInterface
    {
        //交给service处理
        return $categoryService->category_delete($this->request->input('id', -1));
    }
}
