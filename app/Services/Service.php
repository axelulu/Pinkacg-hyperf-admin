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

use App\Model\Attachment;
use App\Model\Setting;
use Donjan\Casbin\Enforcer;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

abstract class Service
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param array $data
     * @param string $message
     * @return Psr7ResponseInterface
     */
    public function success(array $data = [], string $message = '操作成功'): Psr7ResponseInterface
    {
        $res = [
            'code' => 200,
            'message' => $message,
            'result' => $data ?: (object)[],
        ];
        return $this->response->json($res);
    }

    /**
     * @param array $data
     * @param string|null $message
     * @return Psr7ResponseInterface
     */
    public function fail(array $data = [], ?string $message = '操作失败'): Psr7ResponseInterface
    {
        $res = [
            'code' => 401,
            'message' => $message,
            'result' => $data ?: (object)[],
        ];
        return $this->response->json($res);
    }

    public function isJWTUser($request, $JWT, $id): bool
    {
        //判断是否是JWT用户
        $user = $JWT->getParserData();
        $all_permission = $request->getParsedBody();
        var_dump($all_permission);
        if (isset($all_permission['all_permission']) && $all_permission['all_permission'] === 'all_permission') {
            return true;
        }
        if ((!isset($all_permission['all_permission']) || $all_permission['all_permission'] !== 'all_permission') && $user['id'] !== $id) {
            return false;
        }
        return true;
    }

    /**
     * @param $password
     * @return string
     */
    public function passwordHash($password): string
    {
        return sha1(md5($password) . md5(env('APP_PASSWORD_SALT', 'pinkacg')));
    }

    /**
     * @param $id
     * @param $file
     * @param $catType
     * @param int $user_id
     * @return Psr7ResponseInterface|string
     */
    protected function transferFile($id, $file, $catType, int $user_id = 0)
    {
        // 转移文件
        if (isset($file['id'])) {
            $cat_name = \Qiniu\json_decode((Setting::query()->where([['name', 'site_meta']])->get())[0]['value'])->$catType;
            if ($catType === 'user_attachment') {
                $file['user_id'] = $id;
            } elseif ($catType === 'post_attachment') {
                $file['post_id'] = $id;
                $file['user_id'] = $user_id;
            }
            $path = $cat_name . '/' . $file['user_id'] . '/' . $file['post_id'] . '/';
            $oldData = Attachment::query()->select('cat', 'path', 'user_id', 'post_id', 'filename', 'type')->where('id', $file['id'])->first();
            // 转移文件到其他目录
            try {
                if ($this->filesystem->has('uploads/' . $oldData['path'] . $oldData['filename'] . '.' . $oldData['type'])) {
                    $this->filesystem->copy('uploads/' . $oldData['path'] . $oldData['filename'] . '.' . $oldData['type'],
                        'uploads/' . $path . $file['filename'] . '.' . $file['type']);
                    $this->filesystem->delete('uploads/' . $oldData['path'] . $oldData['filename'] . '.' . $oldData['type']);
                }
            } catch (FileExistsException | FileNotFoundException $e) {
                return $this->fail([], '文件转移出错！');
            }
            $file['path'] = $path;
            $file['cat'] = $cat_name;
            Attachment::query()->where('id', $file['id'])->update($file);
            return $file['path'] . $file['filename'] . '.' . $file['type'];
        } else {
            return $file;
        }
    }
}
