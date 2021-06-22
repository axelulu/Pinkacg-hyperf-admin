<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractController;
use App\Model\Setting;
use App\Request\SettingRequest;
use App\Resource\SettingResource;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Phper666\JWTAuth\Middleware\JWTAuthMiddleware;
use App\Middleware\PermissionMiddleware;

/**
 * Class SettingController
 * @package App\Controller\Admin
 * @Controller()
 */
class SettingController extends AbstractController
{
    /**
     * @return \Psr\Http\Message\ResponseInterface
     * @RequestMapping(path="index", methods="get")
     */
    public function index()
    {
        $site = $this->request->input('site', '%');
        $permission = Setting::query()->where([
            ['name', $site]
        ])->get();

        $data = [
            'data' => SettingResource::collection($permission),
        ];
        return $this->success($data);
    }

    /**
     * @param SettingRequest $request
     * @param string $id
     * @return false|\Psr\Http\Message\ResponseInterface|string
     * @RequestMapping(path="update/{id}", methods="put")
     * @Middlewares({
     *     @Middleware(JWTAuthMiddleware::class),
     *     @Middleware(PermissionMiddleware::class)
     * })
     */
    public function update(SettingRequest $request, string $id)
    {
        // 验证
        $data = $request->validated();
        $data['value'] = json_encode($data['value']);
        $flag = Setting::query()->where('name', $id)->update([
            'value'=> $data['value']
        ]);
        if($flag){
            return $this->success();
        }
        return $this->fail();
    }
}
