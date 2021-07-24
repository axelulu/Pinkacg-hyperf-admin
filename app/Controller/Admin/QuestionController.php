<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\QuestionRequest;
use App\Services\QuestionService;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\JWT;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class QuestionController
 * @package App\Controller\Admin
 * @Controller()
 */
class QuestionController extends AbstractController
{
    /**
     * @param QuestionService $quesionService
     * @return ResponseInterface
     * @RequestMapping(path="index", methods="get")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function index(QuestionService $quesionService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $quesionService->index($this->request, $JWT);
    }

    /**
     * @param QuestionService $quesionService
     * @param QuestionRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function create(QuestionService $quesionService, QuestionRequest $request): ResponseInterface
    {
        //交给service处理
        return $quesionService->create($request);
    }

    /**
     * @param QuestionService $quesionService
     * @param QuestionRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(QuestionService $quesionService, QuestionRequest $request, int $id): ResponseInterface
    {
        //交给service处理
        return $quesionService->update($request, $id);
    }

    /**
     * @param QuestionService $quesionService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="delete/{id}", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function delete(QuestionService $quesionService, int $id): ResponseInterface
    {
        //交给service处理
        return $quesionService->delete($id);
    }

    /**
     * @param QuestionService $quesionService
     * @param JWT $JWT
     * @param QuestionRequest $request
     * @return ResponseInterface|void
     * @RequestMapping(path="/admin/submitQuestionResult/index", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function submitQuestionResult(QuestionService $quesionService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $quesionService->submitQuestionResult($this->request, $JWT);
    }
}
