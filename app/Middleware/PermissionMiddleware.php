<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Model\AdminPermission;
use Donjan\Casbin\Enforcer;
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

    public function __construct(JWT $JWT, HttpRequest $request, HttpResponse $response, ContainerInterface $container)
    {
        $this->response = $response;
        $this->request = $request;
        $this->container = $container;
        $this->JWT = $JWT;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $user = $this->JWT->getParserData();
        $role_id = Enforcer::getRolesForUser('roles_' . $user['id'])[0];
        $permission = Enforcer::getPermissionsForUser('permission_' . $role_id);
        foreach ($permission as $k => $v) {
            //每一项权限
            $permission_item = AdminPermission::query()->where('id', $v[3])->first();
            //获取请求的路径
            $path = $this->request->path();
            //判断请求路径与授权路径
            if (strpos($path, $permission_item['path']) !== false) {
                $method = json_decode($permission_item['method']);
                //判断请求方法与授权方法
                if (in_array($this->request->getMethod(), $method)) {
                    return $handler->handle($request);
                }
                return $this->response->json([
                    'code' => 401,
                    'message' => '您无权访问此方法',
                    'result' => [],
                ]);
            }
        }
        return $this->response->json([
            'code' => 401,
            'message' => '您无权访问此路径',
            'result' => [],
        ]);
    }
}