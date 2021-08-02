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
use App\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="question_query", methods="get")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_query(QuestionService $quesionService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $quesionService->question_query($this->request, $JWT);
    }

    /**
     * @param QuestionService $quesionService
     * @param QuestionRequest $request
     * @return ResponseInterface
     * @RequestMapping(path="question_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_create(QuestionService $quesionService, QuestionRequest $request): ResponseInterface
    {
        //交给service处理
        return $quesionService->question_create($request);
    }

    /**
     * @param QuestionService $quesionService
     * @param QuestionRequest $request
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="question_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_update(QuestionService $quesionService, QuestionRequest $request): ResponseInterface
    {
        //交给service处理
        return $quesionService->question_update($request, $this->request->input('id', -1));
    }

    /**
     * @param QuestionService $quesionService
     * @param int $id
     * @return ResponseInterface
     * @RequestMapping(path="question_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_delete(QuestionService $quesionService): ResponseInterface
    {
        //交给service处理
        return $quesionService->question_delete($this->request->input('id', -1));
    }

    /**
     * @param QuestionService $quesionService
     * @param JWT $JWT
     * @param QuestionRequest $request
     * @return ResponseInterface|void
     * @RequestMapping(path="question_submit", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_submit(QuestionService $quesionService, JWT $JWT): ResponseInterface
    {
        //交给service处理
        return $quesionService->question_submit($this->request, $JWT);
    }
}
