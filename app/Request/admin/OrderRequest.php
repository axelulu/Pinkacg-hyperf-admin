<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
//            'id' => 'required',
            'user_id' => 'required',
            'post_id' => 'required',
            'type' => 'required',
            'download_key' => '',
            'credit' => 'required',
//            'updated_at' => 'required'
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
//            'id:required' => '请输入id！',
            'user_id:required' => '请输入用户id！',
            'post_id:required' => '请输入文章id！',
            'type:required' => '请输入类型！',
//            'download_key:required' => '请输入下载下标！',
            'credit:required' => '请输入积分！',
//            'updated_at:required' => '请输入更新时间！'
        ];
    }
}
