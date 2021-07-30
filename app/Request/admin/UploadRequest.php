<?php

declare(strict_types=1);

namespace App\Request\admin;

use Hyperf\Validation\Request\FormRequest;

class UploadRequest extends FormRequest
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
            'file' => 'required|image|min:1|max:4096',
            'id' => '',
            'post_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
            'file.required' => '请输入图片！',
        ];
    }
}
