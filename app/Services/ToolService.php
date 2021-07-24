<?php


namespace App\Services;


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
        $list = Schema::getColumnListing(self::pluralize(substr($result, 6)));
        return $this->success($list);
    }
}