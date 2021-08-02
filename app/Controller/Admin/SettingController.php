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
use App\Middleware\JWTAuthMiddleware;
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
     * @RequestMapping(path="{slug}/setting_query", methods="get")
     */
    public function setting_query(SettingService $settingService, string $slug): ResponseInterface
    {
        //交给service处理
        return $settingService->setting_query($slug);
    }

    /**
     * @param SettingService $settingService
     * @param string $id
     * @return ResponseInterface
     * @RequestMapping(path="{slug}/setting_query_key", methods="get")
     */
    public function setting_query_key(SettingService $settingService, string $slug): ResponseInterface
    {
        //交给service处理
        return $settingService->setting_query_key($slug, $this->request->input('key', ''));
    }

    /**
     * @param SettingService $settingService
     * @param SettingRequest $request
     * @param string $id
     * @return ResponseInterface
     * @RequestMapping(path="{slug}/setting_update", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function setting_update(SettingService $settingService, SettingRequest $request, string $slug): ResponseInterface
    {
        //交给service处理
        return $settingService->setting_update($request, $slug);
    }
}
