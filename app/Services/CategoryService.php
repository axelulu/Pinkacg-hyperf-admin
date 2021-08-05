<?php


namespace App\Services;


use App\Exception\RequestException;
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
    public function category_query($request): ResponseInterface
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 12;

        //获取数据
        try {
            $category = Category::query()
                ->where($this->categoryFilter->apply())
                ->orderBy($orderBy, 'asc')
                ->paginate((int)$pageSize, ['*'], 'pageNo');
            return $this->success(self::getDisplayColumnData(CategoryResource::collection($category)->toArray(), $request, $category));
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param $request
     * @return ResponseInterface
     */
    public function category_create($request): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);

        //创建内容
        try {
            $flag = Category::query()->create($data);
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
    public function category_update($request, $id): ResponseInterface
    {
        //获取验证数据
        $data = self::getValidatedData($request);
        //更新内容
        try {
            $flag = Category::query()->where('id', $id)->update($data);
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
    public function category_delete($id): ResponseInterface
    {
        //获取数据
        try {
            $sonCat = Category::query()->where('son', $id)->first();
            $category = (Category::query()->select('value')->where('id', $id)->first())['value'];
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }

        //是否有子分类
        if ($sonCat) {
            return $this->fail([], '存在子分类');
        }

        //分类是否有文章
        if (Post::query()->where('menu', '"' . $category . '"')->first()) {
            return $this->fail([], '分类存在文章');
        }

        //删除分类
        try {
            $flag = Category::query()->where('id', $id)->delete();
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
     * @param $slug
     * @return ResponseInterface
     */
    public function category_num_query(): ResponseInterface
    {
        $cat = Category::query()->where('son', 0)->get()->toArray();
        foreach ($cat as $k => $v) {
            $num[$k] = Post::query()->where('menu', 'like', '%[' . $v['id'] . ',%')
                ->orWhere('menu', 'like', '%,' . $v['id'] . ']%')
                ->orWhere('menu', 'like', ',%,' . $v['id'] . ',%')
                ->orWhere('menu', 'like', '%[' . $v['id'] . ']%')->count();
        }
        return $this->success([
            'num' => $num
        ]);
    }
}