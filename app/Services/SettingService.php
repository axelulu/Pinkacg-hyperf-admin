<?php


namespace App\Services;

use App\Exception\RequestException;
use App\Model\Setting;
use App\Resource\admin\SettingResource;
use Psr\Http\Message\ResponseInterface;

class SettingService extends Service
{
    /**
     * @param $id
     * @return ResponseInterface
     */
    public function index($id): ResponseInterface
    {
        //获取内容
        try {
            $permission = Setting::query()->where([
                ['name', $id]
            ])->get();

            $data = [
                'data' => SettingResource::collection($permission),
            ];
            return $this->success($data);
        } catch (\Throwable $throwable) {
            throw new RequestException($throwable->getMessage(), $throwable->getCode());
        }
    }

    /**
     * @param $request
     * @param $id
     * @return ResponseInterface
     */
    public function update($request, $id): ResponseInterface
    {
        // 验证
        $data = $request->validated();
        $data['value'] = json_encode($data['value']);

        //更新内容
        try {
            $flag = Setting::query()->where('name', $id)->update([
                'value' => $data['value']
            ]);
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