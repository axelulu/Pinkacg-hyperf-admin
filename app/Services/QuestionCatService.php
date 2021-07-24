<?php


namespace App\Services;


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
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $questionCat = QuestionCat::query()
            ->where($this->questionCatFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $questionCats = $questionCat->toArray();

        return $this->success([
            'pageSize' => $questionCats['per_page'],
            'pageNo' => $questionCats['current_page'],
            'totalCount' => $questionCats['total'],
            'totalPage' => $questionCats['to'],
            'data' => self::getDisplayColumnData(QuestionCatResource::collection($questionCat)->toArray(), $request),
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

        $flag = (new QuestionCatResource(QuestionCat::query()->create($data)))->toResponse();
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

        $flag = QuestionCat::query()->where('id', $id)->update($data);
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
        $flag = QuestionCat::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}