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
        return $categoryService->index($this->request);
    }

    /**
     * @param CategoryService $categoryService
     * @param CategoryRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(CategoryService $categoryService, CategoryRequest $request): ResponseInterface
    {
        //交给service处理
        return $categoryService->create($request);
    }

    /**
     * @param CategoryService $categoryService
     * @param CategoryRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(CategoryService $categoryService, CategoryRequest $request, int $id): ResponseInterface
    {
        //交给service处理
        return $categoryService->update($request, $id);
    }

    /**
     * @param CategoryService $categoryService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="edit/{id}", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function edit(CategoryService $categoryService, int $id): ResponseInterface
    {
        //交给service处理
        return $categoryService->edit($id);
    }

    /**
     * @param CategoryService $categoryService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(CategoryService $categoryService, int $id): ResponseInterface
    {
        //交给service处理
        return $categoryService->delete($id);
    }
}
