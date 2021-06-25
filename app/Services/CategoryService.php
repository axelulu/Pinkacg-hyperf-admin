<?php


namespace App\Services;


use App\Filters\CategoryFilter;
use App\Model\Category;
use App\Resource\CategoryResource;

class CategoryService extends Service
{
    /**
     * @var CategoryFilter
     */
    private $categoryFilter;

    //使用过滤器
    public function __construct(CategoryFilter $categoryFilter)
    {
        $this->categoryFilter = $categoryFilter;
    }

    public function index($request): array
    {
        $orderBy = $request->input('orderBy', 'id');
        $pageSize = $request->query('pageSize') ?? 1000;
        $pageNo = $request->query('pageNo') ?? 1;

        $category = Category::query()
            ->where($this->categoryFilter->apply())
            ->orderBy($orderBy, 'asc')
            ->paginate((int)$pageSize, ['*'], 'page', (int)$pageNo);
        $categorys = $category->toArray();

        return [
            'pageSize' => $categorys['per_page'],
            'pageNo' => $categorys['current_page'],
            'totalCount' => $categorys['total'],
            'totalPage' => $categorys['to'],
            'data' => CategoryResource::collection($category),
        ];
    }
}