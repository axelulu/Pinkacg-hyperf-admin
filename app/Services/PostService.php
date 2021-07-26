<?php


namespace App\Services;

use App\Filters\PostFilter;
use App\Model\Category;
use App\Model\Comment;
use App\Model\Post;
use App\Model\Tag;
use App\Request\home\PostRequest;
use App\Resource\admin\PostResource;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PostService extends Service
{
    /**
     * @Inject
     * @var PostFilter
     */
    protected $postFilter;

    /**
     * @Inject
     * @var PostRequest
     */
    protected $homePostRequest;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;
        if ($menu = $request->input('menu', '')) {
            $menu = (Category::query()->select('id')->where('value', $menu)->get())[0]['id'];
            $permission = Post::query()
                //菜单需要转换为id，单独判断
                ->where('menu', 'like', '%[' . $menu . ',%')
                ->orWhere('menu', 'like', '%,' . $menu . ']%')
                ->orWhere('menu', 'like', ',%,' . $menu . ',%')
                ->where($this->postFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        } else {
            $permission = Post::query()
                //菜单需要转换为id，单独判断
                ->where($this->postFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        }
        $permissions = $permission->toArray();

        return $this->success([
            'pageSize' => $permissions['per_page'],
            'pageNo' => $permissions['current_page'],
            'totalCount' => $permissions['total'],
            'totalPage' => $permissions['to'],
            'data' => self::getDisplayColumnData(PostResource::collection($permission)->toArray(), $request),
        ]);
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);
        $data['tag'] = json_encode($data['tag']);
        $data['menu'] = json_encode($data['menu']);
        $data['download'] = json_encode($data['download']);
        $data['music'] = json_encode($data['music']);
        $data['video'] = json_encode($data['video']);
        if ($request->getAttribute('all_permission') === 'all_permission') {
            $data['status'] = 'publish';
        } else {
            $data['status'] = 'draft';
        }

        //创建文章标签
        $tag = \Qiniu\json_decode($data['tag']);
        foreach ($tag as $k => $v) {
            if (Tag::query()->where('label', $v)->get()->count() === 0) {
                Tag::query()->create([
                    'label' => $v,
                    'value' => $v,
                    'status' => 1,
                ]);
            }
        }

        //创建文章
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

        //更新文章
        $flag = Post::query()->where('id', $flag['id'])->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param JWT $JWT
     * @param $id
     * @return ResponseInterface
     */
    public function update($request, JWT $JWT, $id): ResponseInterface
    {
        //判断是否是JWT用户
        $postAuthorId = (Post::query()->select('author')->where('id', $id)->get()->toArray())[0]['author'];
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $postAuthorId)) {
            return $this->fail([], '用户id错误');
        }

        //获取验证数据
        $data = self::getValidatedData($request);

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

        //更新文章
        $flag = Post::query()->where('id', $id)->update($data);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }

    /**
     * @param $request
     * @param $JWT
     * @param $id
     * @return ResponseInterface
     */
    public function delete($request, $JWT, $id): ResponseInterface
    {
        //判断是否是JWT用户
        $postAuthorId = (Post::query()->select('author')->where('id', $id)->get()->toArray())[0]['author'];
        if (!self::isJWTUser($request, $JWT->getParserData()['id'], $postAuthorId)) {
            return $this->fail([], '用户id错误');
        }

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