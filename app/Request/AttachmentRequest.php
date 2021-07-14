<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class AttachmentRequest extends FormRequest
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
            'title' => 'required',
            'original_name' => 'required',
            'filename' => 'required',
            'path' => 'required',
            'type' => 'required',
            'cat' => 'required',
            'size' => 'required',
            'user_id' => 'required',
//            'updated_at' => 'required',
        ];
    }

    /**
     * 获取已定义验证规则的错误消息
     */
    public function messages(): array
    {
        return [
//            'id:required' => '请输入id！',
            'title:required' => '请输入名称！',
            'original_name:required' => '请输入文件名称！',
            'filename:required' => '请输入文件名称！',
            'path:required' => '请输入文件路径！',
            'type:required' => '请输入文件类型！',
            'cat:required' => '请输入分类！',
            'size:required' => '请输入文件大小！',
            'user_id:required' => '请输入用户id！',
//            'updated_at:required' => '请输入更新时间！',
        ];
    }
}
