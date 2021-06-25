<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Services;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

abstract class Service
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param $password
     * @return string
     */
    public function passwordHash($password): string
    {
        return sha1(md5($password) . md5(env('APP_PASSWORD_SALT', 'pinkacg')));
    }
}
