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
use App\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="question_cat_query", methods="get")
     */
    public function question_cat_query(QuestionCatService $questionCatService): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->question_cat_query($this->request);
    }

    /**
     * @param QuestionCatService $questionCatService
     * @param QuestionCatRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="question_cat_create", methods="post")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_cat_create(QuestionCatService $questionCatService, QuestionCatRequest $request): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->question_cat_create($request);
    }

    /**
     * @param QuestionCatService $questionCatService
     * @param QuestionCatRequest $request
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="question_cat_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_cat_update(QuestionCatService $questionCatService, QuestionCatRequest $request): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->question_cat_update($request, $this->request->input('id', -1));
    }

    /**
     * @param QuestionCatService $questionCatService
     * @param int $id
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="question_cat_delete", methods="delete")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function question_cat_delete(QuestionCatService $questionCatService): \Psr\Http\Message\ResponseInterface
    {
        //交给service处理
        return $questionCatService->question_cat_delete($this->request->input('id', -1));
    }
}
