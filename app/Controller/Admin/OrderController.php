<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\OrderRequest;
use App\Services\OrderService;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class OrderController
 * @package App\Controller\Admin
 * @Controller()
 */
class OrderController extends AbstractController
{
    /**
     * @param OrderService $orderService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(OrderService $orderService): ResponseInterface
    {
        //交给service处理
        return $orderService->index($this->request);
    }

    /**
     * @param OrderService $orderService
     * @param OrderRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(OrderService $orderService, OrderRequest $request): ResponseInterface
    {
        //交给service处理
        return $orderService->create($request);
    }

    /**
     * @param OrderService $orderService
     * @param OrderRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(OrderService $orderService, OrderRequest $request, int $id): ResponseInterface
    {
        //交给service处理
        return $orderService->update($request, $id);
    }

    /**
     * @param OrderService $orderService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(OrderService $orderService, int $id): ResponseInterface
    {
        //交给service处理
        return $orderService->delete($id);
    }

    /**
     * @param OrderService $orderService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="/admin/purchase/create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function purchase(OrderService $orderService)
    {
        //交给service处理
        return $orderService->purchase($this->request);
    }
}
