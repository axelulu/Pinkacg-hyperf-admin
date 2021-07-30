<?php


namespace App\Services;


use App\Model\Post;
use App\Model\User;
use Hyperf\Di\Annotation\Inject;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as response;
use \League\Flysystem\Filesystem;
use QL\QueryList;

class QueryListService extends Service
{
    /**
     * @Inject
     * @var response
     */
    protected $response;

    /**
     * @Inject
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @Inject
     * @var JWT
     */
    protected $JWT;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $data = $request->all();
        //需要采集的目标页面
        $page = $data['pageUrl'];
        $allPage = explode(PHP_EOL, $page);
        foreach ($allPage as $key => $value) {
            //获取页面所有文章链接
            $reg1 = [
                //采集页面链接
                'url' => [$data['pageListClass'], $data['pageListElement']],
            ];
            $rang1 = $data['pageClass'];
            $pageList = QueryList::get($value)->rules($reg1)->range($rang1)->query()->getData();
            $userId = $this->JWT->getParserData()['id'];
            if (isset($userId)) {
                foreach ($pageList as $k => $v) {
                    if ($v['url']) {
                        //采集规则
                        $reg = [
                            //采集文章标题
                            'title' => [$data['postTitleClass'], $data['postTitleElement']],
                            //采集文章正文内容,利用过滤功能去掉文章中的超链接，但保留超链接的文字，并去掉版权、JS代码等无用信息
                            'content' => [$data['postContentClass'], $data['postContentElement'], $data['exceptContentClass']],
                            //喜欢数量
                            'like' => [$data['postLikeClass'], $data['postLikeElement']],
                            //查看数量
                            'view' => [$data['postViewClass'], $data['postViewElement']],
                            'tag' => [$data['tagClass'], 'html']
                        ];
                        $rang = $data['postClass'];
                        $ql = QueryList::get($v['url'])->rules($reg)->range($rang)->query();
                        $queryData = $ql->getData(function ($item) use ($data) {
                            //利用回调函数下载文章中的图片并替换图片路径为本地路径
                            $content = QueryList::html($item['content']);
                            $headerImg = $content->find('img')->map(function ($img) use ($data) {
                                $src = $img->attr('data-srcset');
                                if( $src && substr($src, 0, 7) !== "http://" && substr($src, 0, 8) !== "https://" ) {
                                    $src = 'http://www.acganime.com' . $src;
                                }
                                //文件名称
                                $filename = md5(time() . $src) . '.jpg';
                                //本地文件
                                $fileLocalSrc = 'runtime/uploads/' . $filename;
                                //上传文件地址
                                $fileSrc = 'collection/' . $data['menu'][count($data['menu']) - 1] . '/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
                                $stream = file_get_contents($src);
                                file_put_contents($fileLocalSrc, $stream);
                                $stream = fopen($fileLocalSrc, 'r+');
                                if (!$this->filesystem->has('uploads/' . $fileSrc)) {
                                    $this->filesystem->writeStream(
                                        'uploads/' . $fileSrc,
                                        $stream
                                    );
                                }
                                $img->attr('src', 'https://img.catacg.cn/uploads/' . $fileSrc);
                                $img->attr('data-srcset', 'https://img.catacg.cn/uploads/' . $fileSrc);
                                $img->attr('srcset', 'https://img.catacg.cn/uploads/' . $fileSrc);
                                //删除本地文件
                                unlink($fileLocalSrc);
                                fclose($stream);
                                return $fileSrc;
                            });
                            $item['content'] = $content->find('')->html();

                            $item['header_img'] = $headerImg->toArray()[0];
                            if ($data['tag_status']) {
                                $item['tag'] = QueryList::html($item['tag'])->rules(array(
                                    array($data['postTagClass'], $data['postTagElement'])
                                ))->range('a')->queryData();
                            } else {
                                $item['tag'] = $data['tag'];
                            }

                            return $item;
                        })->toArray();

                        //处理标签
                        if ($data['tag_status']) {
                            foreach ($queryData[0]['tag'] as $kk => $vv) {
                                $queryData[0]['tag'][$kk] = $vv[0];
                            }
                        }

                        $queryData[0]['menu'] = json_encode($data['menu']);
                        $queryData[0]['tag'] = json_encode($queryData[0]['tag']);
                        $queryData[0]['author'] = $userId;
                        $queryData[0]['excerpt'] = $queryData[0]['title'];
                        $queryData[0]['comment_count'] = 0;
                        $queryData[0]['type'] = 'post';
                        $queryData[0]['status'] = 'publish';
                        $queryData[0]['comment_status'] = 1;
                        $queryData[0]['download_status'] = $data['download_status'];
                        if (!$queryData[0]['view']) {
                            $queryData[0]['view'] = 0;
                        }
                        if (!$queryData[0]['like']) {
                            $queryData[0]['like'] = 0;
                        }
                        $result[$k] = $queryData[0];
                        Post::query()->create($result[$k]);
                        sleep(2);
                    } else {
                        return $this->fail([
                            'data' => $result,
                        ]);
                    }
                }
            }
        }

        //返回结果
        return $this->success([
            'data' => $result,
        ]);
    }
}
