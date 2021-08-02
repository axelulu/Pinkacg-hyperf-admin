<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Model\AdminPermission;
use Donjan\Casbin\Enforcer;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Context;
use Phper666\JWTAuth\JWT;
use Psr\Container\ContainerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\HttpServer\Contract\RequestInterface as HttpRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var JWT
     */
    private $JWT;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * PermissionMiddleware constructor.
     * @param JWT $JWT
     * @param HttpRequest $request
     * @param HttpResponse $response
     * @param ContainerInterface $container
     */
    public function __construct(JWT $JWT, HttpRequest $request, HttpResponse $response, ContainerInterface $container)
    {
        $this->response = $response;
        $this->request = $request;
        $this->container = $container;
        $this->JWT = $JWT;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->JWT->getParserData();
        //判断是否拥有管理员权限
        $adminPermission = AdminPermission::query()->select('id')->where([
            'name' => 'ALL',
            'path' => 'ALL',
            'url' => 'ALL'
        ])->first()->toArray();
        if (isset($adminPermission['id'])) {
            $userPermission = Db::table('casbin_rules')->where(['v3' => $adminPermission['id'], 'ptype' => 'p'])->get();
            foreach ($userPermission as $k => $v) {
                if (Db::table('casbin_rules')->where(['v0' => 'roles_' . $user['id'], 'v1' => substr($v->v0, 11, 1)])->get()->count()) {
                    $request = $request->withAttribute('all_permission', 'all_permission');
                    return $handler->handle($request);
                }
            }
        }

        //----------------------------------------------------------------------------------------------------------------------------------------------//

        //获取请求的路径
        $path = $this->request->path();
        var_dump($path);
        $permission = AdminPermission::query()->select('id', 'method', 'key')->where('url', $path)->first()->toArray();
        //获取请求方法
        $method = $this->request->getMethod();

        //判断是否拥有普通权限
        if (in_array($method, \Qiniu\json_decode($permission['method']))) {
            $userPermission = Db::table('casbin_rules')->where(['v3' => $permission['id'], 'ptype' => 'p'])->get();
            foreach ($userPermission as $k => $v) {
                if (Db::table('casbin_rules')->where(['v0' => 'roles_' . $user['id'], 'v1' => substr($v->v0, 11, 1)])->get()->count()) {
                    //无权访问的字段
                    $request = $request->withAttribute('except_columns', $permission['key']);
                    return $handler->handle($request);
                }
            }
        }
        return $this->response->json([
            'code' => 401,
            'message' => '您无权访问此路径',
            'result' => [],
        ]);
    }
}