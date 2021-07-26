<?php

namespace App\Resource\admin;

use App\Model\Attachment;
use App\Model\Category;
use App\Model\Order;
use App\Model\User;
use Hyperf\Resource\Json\JsonResource;
use Phper666\JWTAuth\JWT;
use Hyperf\Di\Annotation\Inject;
use Psr\SimpleCache\InvalidArgumentException;

class PostResource extends JsonResource
{
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
            'authorMeta' => (User::query()->select('name', 'id', 'avatar', 'credit', 'desc', 'background')->where('id', $this->author)->get()->toArray())[0],
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
            'download' => self::getDownload($this->download, $this->id),
            'music' => json_decode($this->music),
            'video' => json_decode($this->video),
            'views' => (int) $this->views,
            'header_img' => $this->header_img,
            'updated_at' => str_replace(array('T','Z'),' ',$this->updated_at),
        ];
    }

    public function getDownload($download, $postId): array
    {
        $download = json_decode($download);
        $newDownload = [];
        foreach ($download as $k => $v) {
            $newDownload[$k] = [
                'name' => $v->name,
                'credit' => $v->credit,
            ];
        }
        try {
            if ($this->JWT->checkToken() !== null) {
                $userId = $this->JWT->getParserData()['id'];
                $orders = Order::query()->where([
                    'user_id' => $userId,
                    'post_id' => $postId
                ])->get()->toArray();
                var_dump($orders);
                if (count($orders) > 0) {
                    foreach ($orders as $kk => $vv) {
                        $newDownload[$vv['download_key']] = $download[$vv['download_key']];
                    }
                }
            }
            return $newDownload;
        } catch (InvalidArgumentException | \Throwable $e) {
            return ['code' => 400];
        }
    }

    /**
     * @param $menu
     * @return array
     */
    private function getMenuMeta($menu): array
    {
        $values = [];
        foreach(json_decode($menu) as $index => $value) {
            array_push($values, Category::query()->select('label', 'value')->where('id', $value)->first()->toArray());
        }
        return $values;
    }
}
