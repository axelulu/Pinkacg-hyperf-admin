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
use App\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="order_query", methods="get")
     */
    public function order_query(OrderService $orderService): ResponseInterface
    {
        //交给service处理
        return $orderService->order_query($this->request);
    }

    /**
     * @param OrderService $orderService
     * @param OrderRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="order_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function order_create(OrderService $orderService, OrderRequest $request): ResponseInterface
    {
        //交给service处理
        return $orderService->order_create($request);
    }

    /**
     * @param OrderService $orderService
     * @param OrderRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="order_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function order_update(OrderService $orderService, OrderRequest $request): ResponseInterface
    {
        //交给service处理
        return $orderService->order_update($request, $this->request->input('id', -1));
    }

    /**
     * @param OrderService $orderService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="order_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function order_delete(OrderService $orderService): ResponseInterface
    {
        //交给service处理
        return $orderService->order_delete($this->request->input('id', -1));
    }
}
