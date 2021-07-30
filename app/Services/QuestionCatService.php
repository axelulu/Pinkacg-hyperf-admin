<?php


namespace App\Services;


use App\Exception\RequestException;
use App\Filters\QuestionCatFilter;
use App\Model\QuestionCat;
use App\Resource\admin\QuestionCatResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class QuestionCatService extends Service
{
    /**
     * @Inject
     * @var QuestionCatFilter
     */
    protected $questionCatFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 12;
        $pageNo = $request->query('pageNo') ?? 1;

        //获取数据
        try {
            $questionCat = QuestionCat::query()
                ->where($this->questionCatFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
            $questionCats = $questionCat->toArray();
            $data = self::getDisplayColumnData(QuestionCatResource::collection($questionCat)->toArray(), $request);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        return $this->success([
            'pageSize' => $questionCats['per_page'],
            'pageNo' => $questionCats['current_page'],
            'totalCount' => $questionCats['total'],
            'totalPage' => $questionCats['to'],
            'data' => $data,
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

        //创建内容
        try {
            $flag = QuestionCat::query()->create($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
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
        //获取验证数据
        $data = self::getValidatedData($request);

        //更新内容
        try {
            $flag = QuestionCat::query()->where('id', $id)->update($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
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
        //删除内容
        try {
            $flag = QuestionCat::query()->where('id', $id)->delete();
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //返回结果
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}