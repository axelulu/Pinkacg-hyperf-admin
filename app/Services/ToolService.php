<?php


namespace App\Services;


use App\Model\Category;
use Hyperf\Database\Schema\Schema;
use Psr\Http\Message\ResponseInterface;

class ToolService extends Service
{
    /**
     * @param $request
     * @return ResponseInterface
     */
    public function getColumnList($request): ResponseInterface
    {
        $data = $request;
        $result = $data['key'];
        try {
            $list = Schema::getColumnListing(self::pluralize(substr($result, 6)));
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
        return $this->success($list);
    }
}