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
            'user_id' => 'required|integer|exists:users,id',
            'post_id' => 'required|integer|exists:posts,id',
            'type' => 'required|string|min:2|max:20',
            'download_key' => 'integer',
            'credit' => 'required|integer',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'user_id:required' => '请输入用户id！',
            'post_id:required' => '请输入文章id！',
            'type:required' => '请输入类型！',
            'credit:required' => '请输入积分！',
        ];
    }
}
