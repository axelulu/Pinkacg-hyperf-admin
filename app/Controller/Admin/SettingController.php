<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Request\admin\SettingRequest;
use App\Services\SettingService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Psr\Http\Message\ResponseInterface;

/**
 * Class SettingController
 * @package App\Controller\Admin
 * @Controller()
 */
class SettingController extends AbstractController
{
    /**
     * @param SettingService $settingService
     * @param string $id
     * @return ResponseInterface
     * @RequestMapping(path="/admin/site_{id}/index", methods="get")
     */
    public function index(SettingService $settingService, string $id): ResponseInterface
    {
        //交给service处理
        return $settingService->index('site_' . $id);
    }

    /**
     * @param SettingService $settingService
     * @param SettingRequest $request
     * @param string $id
     * @return ResponseInterface
     * @RequestMapping(path="/admin/site_{id}/update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(SettingService $settingService, SettingRequest $request, string $id): ResponseInterface
    {
        //交给service处理
        return $settingService->update($request, 'site_' . $id);
    }
}
