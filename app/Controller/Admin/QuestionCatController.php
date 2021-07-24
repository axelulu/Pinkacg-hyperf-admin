<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\QuestionCatRequest;
use App\Services\QuestionCatService;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class QuestionCatController
 * @package App\Controller\Admin
 * @Controller()
 */
class QuestionCatController extends AbstractController
{
    /**
     * @param QuestionCatService $questionCatService
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index(QuestionCatService $questionCatService): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->index($this->request);
    }

    /**
     * @param QuestionCatService $questionCatService
     * @param QuestionCatRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(QuestionCatService $questionCatService, QuestionCatRequest $request): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->create($request);
    }

    /**
     * @param QuestionCatService $questionCatService
     * @param QuestionCatRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(QuestionCatService $questionCatService, QuestionCatRequest $request, int $id): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->update($request, $id);
    }

    /**
     * @param QuestionCatService $questionCatService
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(QuestionCatService $questionCatService, int $id): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->delete($id);
    }
}
