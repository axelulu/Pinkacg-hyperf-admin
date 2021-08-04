<?php

namespace App\Resource\admin;

use App\Exception\RequestException;
use App\Model\Attachment;
use App\Model\Category;
use App\Model\Order;
use App\Model\User;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Redis\Redis;
use Hyperf\Resource\Json\JsonResource;
use Hyperf\Utils\ApplicationContext;
use Phper666\JWTAuth\JWT;
use Hyperf\Di\Annotation\Inject;
use Psr\SimpleCache\InvalidArgumentException;

class PostResource extends JsonResource
{
    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var JWT
     */
    protected $JWT;

    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'author' => $this->author,
            'authorMeta' => User::query()->select('name', 'id', 'avatar', 'credit', 'desc', 'background')->where('id', $this->author)->first()->toArray(),
            'title' => $this->title,
            'content' => $this->content,
            'content_file' => AttachmentPostContentResource::collection(Attachment::query()->where([['user_id', $this->author], ['post_id', $this->id]])->get()),
            'excerpt' => $this->excerpt,
            'type' => $this->type,
            'guid' => $this->guid,
            'comment_count' => (int) $this->comment_count,
            'status' => $this->status,
            'comment_status' => (bool)((int)$this->comment_status),
            'menu' => json_decode($this->menu),
            'menuMeta' => self::getMenuMeta($this->menu),
            'tag' => json_decode($this->tag),
            'download_status' => (bool)((int)$this->download_status),
            'download' => $this->download_status ? self::getDownload($this->download, $this->id) : '',
            'music' => json_decode($this->music),
            'video' => json_decode($this->video),
            'views' => (int) $this->views,
            'credit' => (int) $this->credit,
            'header_img' => $this->header_img,
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }

    /**
     * @param $download
     * @param $postId
     * @return array
     */
    public function getDownload($download, $postId): array
    {
        $download = json_decode($download);
        $newDownload = [];
        if (is_array($download)) {
            foreach ($download as $k => $v) {
                $newDownload[$k] = [
                    'name' => $v->name,
                    'credit' => $v->credit,
                    'code' => 401,
                ];
                $download[$k] = [
                    'code' => 200,
                ];
            }
        }
        try {
            //未登录
            if (empty($this->request->getHeader('authorization'))) {
                return ['code' => 400];
            }
            //登录
            if ($this->request->getHeader('authorization') !== null) {
                $userId = $this->JWT->getParserData()['id'];
                //是管理员
                if (User::isAdmin($userId)) {
                    return $download;
                }
                //不是管理员
                $orders = Order::query()->where([
                    'user_id' => $userId,
                    'post_id' => $postId
                ])->get()->toArray();
                if (count($orders) > 0) {
                    if (is_array($orders)) {
                        foreach ($orders as $kk => $vv) {
                            $newDownload[$vv['download_key']] = $download[$vv['download_key']];
                            $newDownload[$vv['download_key']]['type'] = 200;
                        }
                    }
                }
            }
            return $newDownload;
        } catch (\Throwable $e) {
            throw new RequestException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $menu
     * @return array
     */
    private function getMenuMeta($menu): array
    {
        $values = [];
        if (is_array(json_decode($menu))) {
            foreach (json_decode($menu) as $index => $value) {
                array_push($values, Category::query()->select('label', 'value')->where('id', $value)->first()->toArray());
            }
        }
        return $values;
    }
}
