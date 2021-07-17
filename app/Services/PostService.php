<?php


namespace App\Services;

use App\Filters\PostFilter;
use App\Model\Comment;
use App\Model\Post;
use App\Resource\PostResource;
use Psr\Http\Message\ResponseInterface;

class PostService extends Service
{
    /**
     * @var PostFilter
     */
    private $postFilter;

    //使用过滤器
    public function __construct(PostFilter $postFilter)
    {
        $this->postFilter = $postFilter;
    }

    /**
     * @param $request
     * @return array
     */
    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $permission = Post::query()
            ->where($this->postFilter->apply())
            ->orderBy($orderBy, 'desc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $permissions = $permission->toArray();

        return [
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => PostResource::collection($permission),
        ];
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function create($request): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $flag = Post::query()->create($data)->toArray();

        //转移文件
        foreach ($data['content_file'] as $k => $v) {
            if ($v['filename']) {
                $data['content_file'][$k] = self::transferFile($flag['id'], $v, 'post_attachment', $data['author']);
                $path = $data['content_file'][$k];
                $data['content'] = preg_replace("/<[img|IMG].*?src=[\'|\"](.*?)\/swap\/" . $v['filename'] . ".*?[\'|\"].*?[\/]?>/", '<img src="${1}/' . $path . '${2}" style="max-width:100%">', $data['content']);
            }
        }
        unset($data['content_file']);
        $data['header_img'] = self::transferFile($flag['id'], $data['header_img'], 'post_attachment', $flag['author']);
        $flag = Post::query()->where('id', $flag['id'])->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $id
     * @return ResponseInterface
     */
    public function update($request, $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();

        //转移文件
        foreach ($data['content_file'] as $k => $v) {
            if ($v['filename']) {
                $data['content_file'][$k] = self::transferFile($id, $v, 'post_attachment', $data['author']);
                $path = $data['content_file'][$k];
                $data['content'] = preg_replace("/<[img|IMG].*?src=[\'|\"](.*?)\/swap\/" . $v['filename'] . ".*?[\'|\"].*?[\/]?>/", '<img src="${1}/' . $path . '${2}" style="max-width:100%">', $data['content']);
            }
        }
        unset($data['content_file']);
        $data['header_img'] = self::transferFile($id, $data['header_img'], 'post_attachment', $data['author']);
        $flag = Post::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function delete($id): ResponseInterface
    {
        //判断是否有评论
        if (Comment::query()->where('post_ID', $id)->first()) {
            return $this->fail([], '文章存在评论');
        }
        $flag = Post::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}