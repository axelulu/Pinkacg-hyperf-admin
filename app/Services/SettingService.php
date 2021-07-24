<?php


namespace App\Services;

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
        $permission = Setting::query()->where([
            ['name', $id]
        ])->get();

        $data = [
            'data' => SettingResource::collection($permission),
        ];
        return $this->success($data);
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
        $flag = Setting::query()->where('name', $id)->update([
            'value' => $data['value']
        ]);
        if ($flag) {
            return $this->success();
        }
        return $this->fail();
    }
}