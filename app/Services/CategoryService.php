<?php


namespace App\Services;


use App\Filters\CategoryFilter;
use App\Model\Category;
use App\Model\Post;
use App\Resource\admin\CategoryResource;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;

class CategoryService extends Service
{
    /**
     * @Inject
     * @var CategoryFilter
     */
    protected $categoryFilter;

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function index($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $category = Category::query()
            ->where($this->categoryFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $categorys = $category->toArray();

        return $this->success([
            'pageSize' => $categorys['per_page'],
            'pageNo' => $categorys['current_page'],
            'totalCount' => $categorys['total'],
            'totalPage' => $categorys['to'],
            'data' => self::getDisplayColumnData(CategoryResource::collection($category)->toArray(), $request),
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

        $data['son'] = json_encode($data['son']);
        $flag = Category::query()->create($data);
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

        $data['son'] = json_encode($data['son']);
        $flag = Category::query()->where('id', $id)->update($data);
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
        //是否有子分类
        if (Category::query()->where('son', $id)->first()) {
            return $this->fail([], '存在子分类');
        }
        //分类是否有文章
        $category = (Category::query()->select('value')->where('id', $id)->first())['value'];
        if (Post::query()->where('menu', '"' . $category . '"')->first()) {
            return $this->fail([], '分类存在文章');
        }
        $flag = Category::query()->where('id', $id)->delete();
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}