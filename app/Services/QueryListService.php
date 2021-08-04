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
    public function query_list_query($request): ResponseInterface
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

                        //判断是否存在
                        if (Post::query()->where('title', $ql->getData()[0]['title'])->get()->count() !== 0) {
                            continue;
                        }

                        $queryData = $ql->getData(function ($item) use ($data) {
                            //利用回调函数下载文章中的图片并替换图片路径为本地路径
                            $content = QueryList::html($item['content']);
                            $headerImg = $content->find('img')->map(function ($img) use ($data) {
                                $src = $img->attr($data['postSrcUrlClass']);
                                if ($src && substr($src, 0, 7) !== "http://" && substr($src, 0, 8) !== "https://") {
                                    $src = $data['siteUrl'] . $src;
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
                                $img->attr($data['postSrcUrlClass'], 'https://img.catacg.cn/uploads/' . $fileSrc);
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

                        //获取文章id
                        $postId = explode('/', $v['url']);
                        $postId = $postId[count($postId) - 2];
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
                        if ($data['download_status']) {
                            //获取下载内容
                            $downloadData = QueryList::post('https://www.aidm12.com/wp-json/b2/v1/getDownloadData', [
                                'post_id' => $postId
                            ], [
                                'headers' => [
                                    'authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd3d3LmFpZG0xMi5jb20iLCJpYXQiOjE2Mjc5MjMwNTcsIm5iZiI6MTYyNzkyMzA1NywiZXhwIjoxNjI4NTI3ODU3LCJkYXRhIjp7InVzZXIiOnsiaWQiOiIyMjUzMTIifX19.0vsln7K3THKCarLy2Eb59cc5RVHX5zz5bhm9-BYpgFk',
                                    'Referer' => 'https://www.aidm12.com/download/?post_id=87344&index=0&i=2',
                                    'User-Agent' => 'testing/1.0',
                                    'Accept'     => 'application/json'
                                ]
                            ])->find('')->text();
                            $downloadData = \Qiniu\json_decode($downloadData)[0]->button;

                            foreach ($downloadData as $kkk => $vvv) {
                                $downloadDataItem = QueryList::post('https://www.aidm12.com/wp-json/b2/v1/getDownloadPageData', [
                                    'post_id' => $postId,
                                    'index' => 0,
                                    'i' => $kkk
                                ], [
                                    'headers' => [
                                        'authorization' => 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvd3d3LmFpZG0xMi5jb20iLCJpYXQiOjE2Mjc2ODc4OTQsIm5iZiI6MTYyNzY4Nzg5NCwiZXhwIjoxNjI4MjkyNjk0LCJkYXRhIjp7InVzZXIiOnsiaWQiOiIyMjUzMTIifX19.3xIaqY_iDoMIP8sEPFq7vvmLhmGkiJ8FZ0GBk9eI7NU',
                                        'Referer' => 'https://www.aidm12.com/download/?post_id=87344&index=0&i=2',
                                        'User-Agent' => 'testing/1.0',
                                        'Accept'     => 'application/json'
                                    ]
                                ])->find('')->text();
                                $d = \Qiniu\json_decode($downloadDataItem)->button;
                                $headers = get_headers('https://www.aidm12.com/redirect?token=' . $d->url, TRUE);
                                $download[$kkk] = [
                                    'name' => $d->name,
                                    'link' => $headers['Location'][1],
                                    'pwd' => $d->attr->tq,
                                    'pwd2' => $d->attr->jy,
                                    'credit' => 20
                                ];
                            }

                            $queryData[0]['download'] = json_encode($download);
                        }
                        if (!$queryData[0]['view']) {
                            $queryData[0]['view'] = 0;
                        }
                        if (!$queryData[0]['like']) {
                            $queryData[0]['like'] = 0;
                        }
                        $result[$k] = $queryData[0];
                        Post::query()->create($result[$k]);
                        sleep(1);
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
